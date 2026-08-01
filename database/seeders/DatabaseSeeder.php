<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;
use App\Models\Kiosk;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KioskSeeder::class,
        ]);
        
        $this->command->info('Bắt đầu sinh dữ liệu mẫu (Seeding)...');

        // Tạo 3 tài khoản RBAC
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@huebus.com'],
            ['name' => 'Administrator', 'password' => bcrypt('password'), 'role' => 'admin', 'status' => true]
        );
        \App\Models\User::firstOrCreate(
            ['email' => 'manager@huebus.com'],
            ['name' => 'Manager', 'password' => bcrypt('password'), 'role' => 'manager', 'status' => true]
        );
        \App\Models\User::firstOrCreate(
            ['email' => 'employee@huebus.com'],
            ['name' => 'Employee', 'password' => bcrypt('password'), 'role' => 'employee', 'status' => true]
        );
        $this->command->info('Đã tạo 3 tài khoản Admin, Manager, Employee.');
        // Lấy tất cả Hợp đồng đã có sẵn từ KioskSeeder
        $contracts = Contract::all();

        if ($contracts->isEmpty()) {
            $this->command->warn('Không tìm thấy Hợp đồng nào! Vui lòng kiểm tra lại KioskSeeder.');
            return;
        }

        foreach ($contracts as $contract) {
            // Tạo 5-6 ContractPaymentSchedules cho mỗi hợp đồng
            $numPayments = rand(5, 6);
            for ($i = 0; $i < $numPayments; $i++) {
                $statusType = rand(1, 3); // Trộn lẫn 3 kịch bản UI

                if ($statusType === 1) { // paid
                    ContractPaymentSchedule::factory()->paid()->create([
                        'contract_id' => $contract->id,
                        'amount' => $contract->total_amount / 12, // Hoặc lấy từ deposit/payment_cycle
                        'actual_amount' => $contract->total_amount / 12,
                    ]);
                } elseif ($statusType === 2) { // unpaid (upcoming)
                    ContractPaymentSchedule::factory()->upcoming()->create([
                        'contract_id' => $contract->id,
                        'amount' => $contract->total_amount / 12,
                    ]);
                } else { // unpaid (overdue)
                    // Dùng Faker để sinh ngày trong quá khứ như yêu cầu
                    $faker = \Faker\Factory::create();
                    ContractPaymentSchedule::factory()->overdue()->create([
                        'contract_id' => $contract->id,
                        'amount' => $contract->total_amount / 12,
                        'due_date' => $faker->dateTimeBetween('-60 days', '-1 days')->format('Y-m-d'),
                    ]);
                }
            }
        }

        $this->command->info('Đã seed thành công ContractPaymentSchedules cho các Hợp đồng thật có sẵn!');
    }
}
