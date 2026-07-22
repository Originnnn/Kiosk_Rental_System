<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunDailyETL extends Command
{
    /**
     * Tên lệnh (signature) dùng để chạy trên terminal.
     */
    protected $signature = 'etl:run';

    /**
     * Mô tả lệnh.
     */
    protected $description = 'Chạy luồng ETL hàng ngày để bơm dữ liệu từ OLTP sang OLAP (Star Schema)';

    /**
     * Thực thi lệnh.
     */
    public function handle()
    {
        $this->info("========================================");
        $this->info("🚀 BẮT ĐẦU TIẾN TRÌNH DAILY ETL...");
        $this->info("========================================");

        DB::beginTransaction();

        try {
            // ==========================================
            // 1. EXTRACT & LOAD: Bảng `dim.kiosk`
            // ==========================================
            $this->info("[1/3] Đang đồng bộ cấu hình Kiosk vào Dim.Kiosk...");
            DB::statement("
                INSERT IGNORE INTO kiosk_dwh.`dim.kiosk` (`OriginalKioskID`, `Code`, `Zone`, `BasePrice`)
                SELECT 
                    k.id,
                    k.code,
                    IFNULL(kp.zone, 'Unknown'),
                    k.price
                FROM kiosk_rental_system.`kiosks` k
                LEFT JOIN kiosk_rental_system.`kiosk_positions` kp ON k.id = kp.kiosk_id
            ");

            // ==========================================
            // 2. EXTRACT & LOAD: Bảng `dim.date`
            // ==========================================
            $this->info("[2/3] Đang cập nhật Dimension Thời gian (Dim.Date)...");
            DB::statement("
                INSERT IGNORE INTO kiosk_dwh.`dim.date` (`DateKey`, `FullDate`, `Day`, `Month`, `Year`)
                SELECT DISTINCT
                    DATE_FORMAT(created_at, '%Y%m%d') AS DateKey,
                    DATE(created_at) AS FullDate,
                    DAY(created_at) AS Day,
                    MONTH(created_at) AS Month,
                    YEAR(created_at) AS Year
                FROM kiosk_rental_system.`booking_requests`
                WHERE status = 'resolved'
            ");

            // ==========================================
            // 3. EXTRACT, TRANSFORM & LOAD: Bảng `fact.rental`
            // ==========================================
            $this->info("[3/3] Đang bơm dữ liệu hợp đồng thuê (Resolved) vào Fact.Rental...");
            // Lưu ý: bảng gốc trong CSDL của bạn là booking_requests
            DB::statement("
                INSERT INTO kiosk_dwh.`fact.rental` (`DateKey`, `KioskKey`, `DurationMonths`, `TotalRevenue`)
                SELECT 
                    -- Transform 1: Format ngày thành YYYYMMDD để nối với Dim.Date
                    DATE_FORMAT(r.created_at, '%Y%m%d') AS DateKey,
                    
                    -- Transform 2: Lookup KioskKey thay vì dùng ID OLTP
                    dk.KioskKey,
                    
                    r.duration_months,
                    
                    -- Transform 3: Tính TotalRevenue = Thời gian * Giá gốc tại thời điểm thuê
                    (r.duration_months * k.price) AS TotalRevenue
                    
                FROM kiosk_rental_system.`booking_requests` r
                JOIN kiosk_rental_system.`kiosks` k ON r.kiosk_id = k.id
                JOIN kiosk_dwh.`dim.kiosk` dk ON dk.OriginalKioskID = k.id
                WHERE r.status = 'resolved'
            ");

            DB::commit();
            $this->info("========================================");
            $this->info("✅ HOÀN TẤT TIẾN TRÌNH ETL THÀNH CÔNG!");
            $this->info("========================================");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ LỖI NGHIÊM TRỌNG TRONG QUÁ TRÌNH ETL:");
            $this->error($e->getMessage());
        }
    }
}
