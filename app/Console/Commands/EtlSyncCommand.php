<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ContractPaymentSchedule;
use Carbon\Carbon;

class EtlSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dwh:etl-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ dữ liệu từ OLTP sang DWH';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("========================================");
        $this->info("🚀 BẮT ĐẦU TIẾN TRÌNH ETL ĐỒNG BỘ OLTP -> DWH");
        $this->info("========================================");

        $dwh = DB::connection('dwh');

        // Tạo cấu trúc bảng nếu chưa có
        $this->setupSchema($dwh);

        // 1. EXTRACT
        $this->info("[1/4] Đang lấy dữ liệu thanh toán từ OLTP (Extract)...");
        // Lấy những giao dịch đã được thanh toán (status = paid) và có ngày thanh toán
        $payments = ContractPaymentSchedule::with(['contract.customer', 'contract.kiosk'])
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->get();

        if ($payments->isEmpty()) {
            $this->info("Không có dữ liệu thanh toán mới nào để đồng bộ.");
            return;
        }
        
        $this->info("Tìm thấy {$payments->count()} giao dịch thanh toán.");

        $dwh->beginTransaction();

        try {
            // 2. TRANSFORM & LOAD: Dimensions
            $this->info("[2/4] Đang xử lý các Dimensions (Khách hàng, Kiosk)...");
            
            foreach ($payments as $payment) {
                $contract = $payment->contract;
                if (!$contract) continue;
                
                $customer = $contract->customer;
                $kiosk = $contract->kiosk;

                // Load Customer
                if ($customer) {
                    $dwh->statement("
                        INSERT INTO `Dim.Customer` (`OriginalID`, `Name`, `Phone`, `IDCard`) 
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE `Name` = VALUES(`Name`), `Phone` = VALUES(`Phone`)
                    ", [
                        $customer->id, 
                        $customer->name, 
                        $customer->phone, 
                        $customer->id_card_number
                    ]);
                }

                // Load Kiosk
                if ($kiosk) {
                    $dwh->statement("
                        INSERT INTO `Dim.Kiosk` (`OriginalID`, `Code`, `Area`, `Price`) 
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE `Code` = VALUES(`Code`), `Price` = VALUES(`Price`)
                    ", [
                        $kiosk->id, 
                        $kiosk->code, 
                        $kiosk->area, 
                        $kiosk->price
                    ]);
                }
            }

            // 3. TRANSFORM & LOAD: Time Dimension
            $this->info("[3/4] Đang phân tích thời gian và Load vào Dim.Date...");
            foreach ($payments as $payment) {
                $paidAt = Carbon::parse($payment->paid_at);
                $dateKey = (int) $paidAt->format('Ymd');
                
                $dwh->statement("
                    INSERT IGNORE INTO `Dim.Date` (`DateKey`, `FullDate`, `Day`, `Month`, `Year`, `Quarter`) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ", [
                    $dateKey,
                    $paidAt->format('Y-m-d'),
                    $paidAt->day,
                    $paidAt->month,
                    $paidAt->year,
                    ceil($paidAt->month / 3)
                ]);
            }

            // 4. TRANSFORM & LOAD: Fact Table
            $this->info("[4/4] Đang liên kết Surrogate Keys và Load vào Fact.Rental...");
            foreach ($payments as $payment) {
                $contract = $payment->contract;
                if (!$contract || !$contract->customer || !$contract->kiosk) continue;

                $paidAt = Carbon::parse($payment->paid_at);
                $dateKey = (int) $paidAt->format('Ymd');

                // Lấy surrogate keys (do ID trong bảng Dim tự động tăng, ta lấy theo OriginalID)
                $dimCustomer = $dwh->selectOne("SELECT `CustomerKey` FROM `Dim.Customer` WHERE `OriginalID` = ?", [$contract->customer->id]);
                $dimKiosk = $dwh->selectOne("SELECT `KioskKey` FROM `Dim.Kiosk` WHERE `OriginalID` = ?", [$contract->kiosk->id]);

                if ($dimCustomer && $dimKiosk) {
                    // Dùng TotalRevenue để tương thích với Dashboard hiện tại
                    $dwh->statement("
                        INSERT INTO `Fact.Rental` (`PaymentID`, `CustomerKey`, `KioskKey`, `DateKey`, `TotalRevenue`) 
                        VALUES (?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE `TotalRevenue` = VALUES(`TotalRevenue`)
                    ", [
                        $payment->id,
                        $dimCustomer->CustomerKey,
                        $dimKiosk->KioskKey,
                        $dateKey,
                        $payment->actual_amount
                    ]);
                }
            }

            $dwh->commit();
            $this->info("========================================");
            $this->info("✅ HOÀN TẤT TIẾN TRÌNH ETL THÀNH CÔNG!");
            $this->info("========================================");

        } catch (\Exception $e) {
            $dwh->rollBack();
            $this->error("❌ LỖI TRONG QUÁ TRÌNH ETL: " . $e->getMessage());
        }
    }

    private function setupSchema($dwh)
    {
        $dwh->statement("
            CREATE TABLE IF NOT EXISTS `Dim.Customer` (
                `CustomerKey` INT AUTO_INCREMENT PRIMARY KEY,
                `OriginalID` INT UNIQUE,
                `Name` VARCHAR(255),
                `Phone` VARCHAR(50),
                `IDCard` VARCHAR(50)
            )
        ");

        $dwh->statement("
            CREATE TABLE IF NOT EXISTS `Dim.Kiosk` (
                `KioskKey` INT AUTO_INCREMENT PRIMARY KEY,
                `OriginalID` INT UNIQUE,
                `Code` VARCHAR(50),
                `Area` DECIMAL(10,2),
                `Price` DECIMAL(15,2)
            )
        ");

        $dwh->statement("
            CREATE TABLE IF NOT EXISTS `Dim.Date` (
                `DateKey` INT PRIMARY KEY,
                `FullDate` DATE,
                `Day` INT,
                `Month` INT,
                `Year` INT,
                `Quarter` INT
            )
        ");

        $dwh->statement("
            CREATE TABLE IF NOT EXISTS `Fact.Rental` (
                `PaymentKey` INT AUTO_INCREMENT PRIMARY KEY,
                `PaymentID` INT UNIQUE,
                `CustomerKey` INT,
                `KioskKey` INT,
                `DateKey` INT,
                `TotalRevenue` DECIMAL(15,2),
                FOREIGN KEY (`CustomerKey`) REFERENCES `Dim.Customer`(`CustomerKey`),
                FOREIGN KEY (`KioskKey`) REFERENCES `Dim.Kiosk`(`KioskKey`),
                FOREIGN KEY (`DateKey`) REFERENCES `Dim.Date`(`DateKey`)
            )
        ");
    }
}
