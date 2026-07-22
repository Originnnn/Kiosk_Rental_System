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
        // 1. Tạo 20 Customer
        $customers = Customer::factory(20)->create();
        $this->command->info('Đã tạo 20 Customers.');

        // 2. Lấy danh sách Kiosk đang có status 'rented' hoặc 'reserved'
        $kiosks = Kiosk::whereIn('status', ['rented', 'reserved'])->get();

        if ($kiosks->isEmpty()) {
            $this->command->warn('Không tìm thấy Kiosk nào có status rented hoặc reserved trong cơ sở dữ liệu!');
            return;
        }

        foreach ($kiosks as $kiosk) {
            // Lấy ngẫu nhiên 1 Customer
            $randomCustomer = $customers->random();

            // Tạo RentalRequest để tránh lỗi foreign key hoặc cột not null
            $rentalRequest = \App\Models\RentalRequest::create([
                'reference_code' => 'REQ-' . rand(1000, 9999),
                'kiosk_id' => $kiosk->id,
                'customer_id' => $randomCustomer->id,
                'contact_name' => $randomCustomer->name,
                'contact_email' => $randomCustomer->email,
                'contact_phone' => $randomCustomer->phone,
                'desired_start' => now()->subMonths(3),
                'desired_end' => now()->addYears(1),
                'status' => 'approved',
            ]);

            // 3. Tạo 1 Contract cho mỗi Kiosk
            $contract = Contract::factory()->create([
                'rental_request_id' => $rentalRequest->id,
                'kiosk_id' => $kiosk->id,
                'customer_id' => $randomCustomer->id,
                'total_amount' => $kiosk->price * 12, // Giả sử hợp đồng 1 năm
            ]);

            // 4. Tạo 5-6 ContractPaymentSchedules
            $numPayments = rand(5, 6);
            for ($i = 0; $i < $numPayments; $i++) {
                $statusType = rand(1, 3); // Trộn lẫn 3 kịch bản UI

                if ($statusType === 1) { // paid
                    ContractPaymentSchedule::factory()->paid()->create([
                        'contract_id' => $contract->id,
                        'amount' => $kiosk->price,
                        'actual_amount' => $kiosk->price,
                    ]);
                } elseif ($statusType === 2) { // unpaid (upcoming)
                    ContractPaymentSchedule::factory()->upcoming()->create([
                        'contract_id' => $contract->id,
                        'amount' => $kiosk->price,
                    ]);
                } else { // unpaid (overdue)
                    ContractPaymentSchedule::factory()->overdue()->create([
                        'contract_id' => $contract->id,
                        'amount' => $kiosk->price,
                    ]);
                }
            }
        }

        $this->command->info('Đã seed thành công Contract và ContractPaymentSchedules từ các Kiosk có sẵn!');
    }
}
