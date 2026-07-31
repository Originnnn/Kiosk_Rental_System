<?php

namespace Database\Seeders;

use App\Models\Kiosk;
use App\Models\KioskPosition;
use App\Models\Customer;
use App\Models\Contract;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class KioskSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $faker = Faker::create('vi_VN');
            
            // Mảng trạng thái giả định để random cho phong phú
            $statuses = ['available', 'reserved', 'rented'];

            // Dữ liệu 22 khách hàng thật
            $realCustomers = [
                'K-25' => ['name' => 'Tôn Nữ Thị Loan', 'business' => 'Ăn uống, kinh doanh phụ tùng ô tô'],
                'K-19' => ['name' => 'Mai Thị Thanh Hà', 'business' => 'Giải khát'],
                'K-17' => ['name' => 'Nguyễn Thị Ký-Quốc Thưởng', 'business' => 'Văn phòng giao dịch'],
                'K-23' => ['name' => 'Ngô Tá Lành', 'business' => 'Garage sửa chữa ô tô'],
                'K-10' => ['name' => 'Võ Bá Phước', 'business' => 'Garage điện ô tô'],
                'K-14' => ['name' => 'Nguyễn Văn Cương-Quốc Trường', 'business' => 'Văn phòng giao dịch'],
                'K-11' => ['name' => 'Nguyễn Thị Xuân Nhi', 'business' => 'Kho vận tuyến Bình Phước'],
                'K-13' => ['name' => 'Nguyễn Thị Lan', 'business' => 'Văn phòng giao dịch xe Quốc Khánh'],
                'K-21' => ['name' => 'Lê Văn Hiếu', 'business' => 'Garage điện ô tô'],
                'K-24' => ['name' => 'Lê Hồng Thạnh', 'business' => 'Garage điện ô tô'],
                'K-12' => ['name' => 'Hoàng Xuân Hùng', 'business' => 'Giải khát'],
                'K-22' => ['name' => 'Bùi Văn Tân-Phú Lộc', 'business' => 'Văn phòng giao dịch xe Phú Lộc'],
                'K-32' => ['name' => 'Nguyễn Thị Nhỏ-Ăn uống', 'business' => 'Ăn uống giải khát'],
                'K-9'  => ['name' => 'Nguyễn Oanh (café Win)', 'business' => 'Ăn uống giải khát'],
                'K-31' => ['name' => 'Hoàng Thị Kim Cúc', 'business' => 'Ăn uống giải khát'],
                'K-34' => ['name' => 'Huỳnh Quốc Khoa', 'business' => 'Ăn uống giải khát'],
                'K-30' => ['name' => 'Phòng nghỉ Lái phụ xe Phương Trang', 'business' => 'Phụ lục hợp đồng'],
                'K-18' => ['name' => 'Ngọc Thông', 'business' => 'Đắk Lắk'],
                'K-20' => ['name' => 'Ngọc Thông Một', 'business' => 'Đắk Lắk'],
                'K-15' => ['name' => 'Diên Hồng', 'business' => 'Gia Lai'],
                'K-16' => ['name' => 'Quốc Khánh', 'business' => 'Đà Lạt'],
                'K-29' => ['name' => 'Phòng nghỉ Lái phụ xe Xuân Phúc', 'business' => 'Tam Kỳ'],
            ];

            // ==========================================
            // KIOSK ĐẶC BIỆT: K-9
            // ==========================================
            $k9Code = 'K-9';
            $k9Kiosk = Kiosk::create([
                'code' => $k9Code,
                'name' => $k9Code,
                'description' => 'Ki ốt K-9',
                'area' => rand(150, 250) / 10,
                'price' => rand(40, 80) * 100000,
                'status' => 'rented',
            ]);
            
            KioskPosition::create([
                'kiosk_id' => $k9Kiosk->id,
                'x' => 1529,
                'y' => 49,
                'width' => 103,
                'height' => 38,
                'zone' => 'Khu B',
            ]);

            $cData = $realCustomers[$k9Code];
            $k9Customer = Customer::create([
                'name' => $cData['name'],
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'id_card_number' => $faker->numerify('############'),
                'status' => 'active',
            ]);

            $startDate = Carbon::create(rand(2025, 2026), rand(1, 12), rand(1, 28));
            $endDate = $startDate->copy()->addYears(rand(1, 2));

            Contract::create([
                'reference_code' => 'HD-' . date('Y') . '-' . str_pad($k9Kiosk->id, 3, '0', STR_PAD_LEFT),
                'customer_id' => $k9Customer->id,
                'kiosk_id' => $k9Kiosk->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'deposit_amount' => $k9Kiosk->price * 2,
                'total_amount' => $k9Kiosk->price * 12,
                'payment_cycle' => 'monthly',
                'status' => 'active',
                'terms' => 'Ngành nghề: ' . $cData['business'],
            ]);

            // ==========================================
            // KHU A: Dãy Kiosk K-10 đến K-25
            // ==========================================
            $zoneA_startX = 370;
            $zoneA_deltaX = 63; // Khoảng cách giữa 2 kiosk
            $zoneA_y = 49;
            $zoneA_width = 61;
            $zoneA_height = 76;
            $zoneA_name = 'Khu A';

            for ($i = 10; $i <= 25; $i++) {
                $currentX = $zoneA_startX + (($i - 10) * $zoneA_deltaX);
                
                $fakePrice = rand(40, 80) * 100000; 
                $fakeArea = rand(150, 250) / 10;
                $kioskCode = 'K-' . $i;
                
                $hasRealCustomer = isset($realCustomers[$kioskCode]);
                $randomStatus = $hasRealCustomer ? 'rented' : $statuses[array_rand($statuses)];

                $kiosk = Kiosk::create([
                    'code' => $kioskCode,
                    'name' => $kioskCode,
                    'description' => 'Ki ốt khu vực mặt tiền số ' . $i,
                    'area' => $fakeArea,
                    'price' => $fakePrice,
                    'status' => $randomStatus,
                ]);
                
                KioskPosition::create([
                    'kiosk_id' => $kiosk->id,
                    'x' => $currentX,
                    'y' => $zoneA_y,
                    'width' => $zoneA_width,
                    'height' => $zoneA_height,
                    'zone' => $zoneA_name,
                ]);

                if ($hasRealCustomer) {
                    $cData = $realCustomers[$kioskCode];
                    $customer = Customer::create([
                        'name' => $cData['name'],
                        'email' => $faker->unique()->safeEmail,
                        'phone' => $faker->phoneNumber,
                        'address' => $faker->address,
                        'id_card_number' => $faker->numerify('############'),
                        'status' => 'active',
                    ]);

                    $startDate = Carbon::create(rand(2025, 2026), rand(1, 12), rand(1, 28));
                    $endDate = $startDate->copy()->addYears(rand(1, 2));

                    Contract::create([
                        'reference_code' => 'HD-' . date('Y') . '-' . str_pad($kiosk->id, 3, '0', STR_PAD_LEFT),
                        'customer_id' => $customer->id,
                        'kiosk_id' => $kiosk->id,
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'deposit_amount' => $kiosk->price * 2,
                        'total_amount' => $kiosk->price * 12,
                        'payment_cycle' => 'monthly',
                        'status' => 'active',
                        'terms' => 'Ngành nghề: ' . $cData['business'],
                    ]);
                }
            }

            // ==========================================
            // KHU B: Dãy Kiosk K-26 đến K-34
            // ==========================================
            $zoneB_startX = 1635;
            $zoneB_startY = 49;
            $zoneB_deltaY = 40; // Khoảng cách y
            $zoneB_width = 126;
            $zoneB_height = 38;
            $zoneB_name = 'Khu B';

            for ($i = 26; $i <= 34; $i++) {
                $currentY = $zoneB_startY + (($i - 26) * $zoneB_deltaY);

                $fakePrice = rand(40, 80) * 100000; 
                $fakeArea = rand(150, 250) / 10;
                $kioskCode = 'K-' . $i;

                $hasRealCustomer = isset($realCustomers[$kioskCode]);
                $randomStatus = $hasRealCustomer ? 'rented' : $statuses[array_rand($statuses)];

                $kiosk = Kiosk::create([
                    'code' => $kioskCode,
                    'name' => $kioskCode,
                    'description' => 'Ki ốt dãy 26-34',
                    'area' => $fakeArea,
                    'price' => $fakePrice,
                    'status' => $randomStatus,
                ]);
                
                KioskPosition::create([
                    'kiosk_id' => $kiosk->id,
                    'x' => $zoneB_startX,
                    'y' => $currentY,
                    'width' => $zoneB_width,
                    'height' => $zoneB_height,
                    'zone' => $zoneB_name,
                ]);

                if ($hasRealCustomer) {
                    $cData = $realCustomers[$kioskCode];
                    $customer = Customer::create([
                        'name' => $cData['name'],
                        'email' => $faker->unique()->safeEmail,
                        'phone' => $faker->phoneNumber,
                        'address' => $faker->address,
                        'id_card_number' => $faker->numerify('############'),
                        'status' => 'active',
                    ]);

                    $startDate = Carbon::create(rand(2025, 2026), rand(1, 12), rand(1, 28));
                    $endDate = $startDate->copy()->addYears(rand(1, 2));

                    Contract::create([
                        'reference_code' => 'HD-' . date('Y') . '-' . str_pad($kiosk->id, 3, '0', STR_PAD_LEFT),
                        'customer_id' => $customer->id,
                        'kiosk_id' => $kiosk->id,
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'deposit_amount' => $kiosk->price * 2,
                        'total_amount' => $kiosk->price * 12,
                        'payment_cycle' => 'monthly',
                        'status' => 'active',
                        'terms' => 'Ngành nghề: ' . $cData['business'],
                    ]);
                }
            }
        });
    }
}