<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kiosk;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;
use Carbon\Carbon;

class MassiveDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Bắt đầu quá trình Massive Data Seeding...');

        // 1. Truncate bảng (tạm tắt khóa ngoại)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ContractPaymentSchedule::truncate();
        Contract::truncate();
        Customer::truncate();
        Kiosk::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Đã truncate dữ liệu cũ.');

        DB::transaction(function () {

            // 2. Tạo 50 Kiosks
            $kiosks = Kiosk::factory(50)->create();
            $this->command->info('Đã tạo 50 Kiosks.');

            // 3. Tạo 200 Customers
            $customers = Customer::factory(200)->create();
            $this->command->info('Đã tạo 200 Customers.');

            // Lấy ID để pick ngẫu nhiên
            $kioskIds = $kiosks->pluck('id')->toArray();
            $customerIds = $customers->pluck('id')->toArray();

            // 4. Tạo 300 Contracts
            $contracts = Contract::factory(300)->make()->each(function ($contract) use ($kioskIds, $customerIds) {
                $contract->kiosk_id = $kioskIds[array_rand($kioskIds)];
                $contract->customer_id = $customerIds[array_rand($customerIds)];
                $contract->save();
            });
            $this->command->info('Đã tạo 300 Contracts.');

            // 5. Sinh ContractPaymentSchedules
            $this->command->info('Đang sinh các kỳ thanh toán (Payments) cho 300 Hợp đồng...');
            $paymentsToInsert = [];
            $now = Carbon::now();

            foreach ($contracts as $contract) {
                // Giá của Kiosk cho hợp đồng này
                $kiosk = Kiosk::find($contract->kiosk_id);
                $amountPerMonth = $kiosk ? $kiosk->price : 5000000;

                $currentDate = Carbon::parse($contract->start_date);
                $endDate = Carbon::parse($contract->end_date);

                while ($currentDate <= $endDate) {
                    $dueDate = $currentDate->copy();
                    $isPast = $dueDate < $now;
                    
                    $paymentsToInsert[] = [
                        'contract_id' => $contract->id,
                        'due_date' => $dueDate->format('Y-m-d'),
                        'amount' => $amountPerMonth,
                        'actual_amount' => $isPast ? $amountPerMonth : 0,
                        'status' => $isPast ? 'paid' : 'unpaid',
                        'paid_at' => $isPast ? $dueDate->copy()->addDays(rand(0, 5))->format('Y-m-d H:i:s') : null,
                        'payment_method' => $isPast ? 'bank_transfer' : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $currentDate->addMonth();
                }

                // Chèn theo từng đợt để tránh quá tải bộ nhớ
                if (count($paymentsToInsert) >= 1000) {
                    ContractPaymentSchedule::insert($paymentsToInsert);
                    $paymentsToInsert = [];
                }
            }

            // Chèn phần còn lại
            if (count($paymentsToInsert) > 0) {
                ContractPaymentSchedule::insert($paymentsToInsert);
            }

            $this->command->info('Đã hoàn tất sinh dữ liệu thanh toán!');
        });

        $this->command->info('Massive Data Seeding hoàn thành xuất sắc!');
    }
}
