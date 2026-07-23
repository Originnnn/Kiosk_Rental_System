<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kiosk;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;
use App\Models\RentalRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Bắt đầu làm sạch dữ liệu (giữ lại User và Kiosk)...');

        // 1. Truncate bảng (tạm tắt khóa ngoại)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ContractPaymentSchedule::truncate();
        Contract::truncate();
        RentalRequest::truncate();
        Customer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Đã xóa dữ liệu Khách hàng, Hợp đồng, Yêu cầu thuê, Thanh toán.');

        DB::transaction(function () {
            // 2. Tạo dữ liệu Khách hàng (Huế)
            $hueCustomers = [
                ['name' => 'Nguyễn Văn An', 'email' => 'nguyenvanan.hue@gmail.com', 'phone' => '0935123456', 'address' => '12 Lê Lợi, Phường Vĩnh Ninh, TP Huế', 'id_card_number' => '190123456789'],
                ['name' => 'Trần Thị Bình', 'email' => 'tranthibinh.bxh@gmail.com', 'phone' => '0905987654', 'address' => '45 Hùng Vương, Phường Phú Nhuận, TP Huế', 'id_card_number' => '190987654321'],
                ['name' => 'Lê Hoàng Cường', 'email' => 'cuongle.75@yahoo.com', 'phone' => '0979112233', 'address' => '78 Nguyễn Huệ, Phường Vĩnh Ninh, TP Huế', 'id_card_number' => '191223344556'],
                ['name' => 'Phạm Thu Dung', 'email' => 'dungpham.kiosk@gmail.com', 'phone' => '0944556677', 'address' => '102 Đinh Tiên Hoàng, Phường Đông Ba, TP Huế', 'id_card_number' => '192334455667'],
                ['name' => 'Hoàng Minh Tuấn', 'email' => 'tuanhoang.bussiness@gmail.com', 'phone' => '0988776655', 'address' => '34 Bến Nghé, Phường Phú Hội, TP Huế', 'id_card_number' => '193445566778'],
                ['name' => 'Công ty TNHH MTV Thành Đạt', 'email' => 'contact@thanhdat.hue.vn', 'phone' => '02343811222', 'address' => '56 Nguyễn Sinh Cung, Phường Vỹ Dạ, TP Huế', 'id_card_number' => '0101234567'],
            ];

            $customerModels = [];
            foreach ($hueCustomers as $cData) {
                $customerModels[] = Customer::create([
                    'name' => $cData['name'],
                    'email' => $cData['email'],
                    'phone' => $cData['phone'],
                    'address' => $cData['address'],
                    'id_card_number' => $cData['id_card_number'],
                    'status' => 'active',
                ]);
            }
            $this->command->info('Đã tạo ' . count($customerModels) . ' Khách hàng (Huế).');

            // 3. Lấy Kiosk
            $kiosks = Kiosk::all();
            if ($kiosks->isEmpty()) {
                $this->command->error('Chưa có dữ liệu Kiosk. Vui lòng seed Kiosk trước!');
                return;
            }

            // Chuyển tất cả kiosk về trạng thái available
            Kiosk::query()->update(['status' => 'available']);
            $kiosks = Kiosk::all(); // Reload

            // 4. Tạo Hợp đồng & Thanh toán
            // Cho 4 khách hàng đầu tiên thuê 4 kiosk ngẫu nhiên
            $activeKiosks = $kiosks->random(4);
            $now = Carbon::now();

            foreach ($activeKiosks as $index => $kiosk) {
                $customer = $customerModels[$index];
                
                $startDate = $now->copy()->subMonths(rand(1, 5))->startOfMonth();
                $endDate = $startDate->copy()->addYears(1);

                $contract = Contract::create([
                    'reference_code' => 'HD-' . date('Y') . '-' . str_pad($kiosk->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($customer->id, 3, '0', STR_PAD_LEFT),
                    'customer_id' => $customer->id,
                    'kiosk_id' => $kiosk->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'deposit_amount' => $kiosk->price * 2,
                    'total_amount' => $kiosk->price * 12,
                    'payment_cycle' => 'monthly',
                    'status' => 'active',
                    'terms' => 'Hợp đồng thuê Kiosk tại Bến Xe Huế. Bên B tuân thủ các quy định về an ninh trật tự và PCCC.',
                ]);

                // Đổi trạng thái Kiosk
                $kiosk->update(['status' => 'rented']);

                // Tạo kỳ thanh toán (thanh toán mỗi tháng)
                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    $dueDate = $currentDate->copy();
                    $isPast = $dueDate < $now;
                    
                    ContractPaymentSchedule::create([
                        'contract_id' => $contract->id,
                        'due_date' => $dueDate->format('Y-m-d'),
                        'amount' => $kiosk->price,
                        'actual_amount' => $isPast ? $kiosk->price : 0,
                        'status' => $isPast ? 'paid' : 'unpaid',
                        'paid_at' => $isPast ? $dueDate->copy()->addDays(rand(0, 5))->format('Y-m-d H:i:s') : null,
                        'payment_method' => $isPast ? (rand(0,1) ? 'bank_transfer' : 'cash') : null,
                    ]);

                    $currentDate->addMonth();
                }
            }
            $this->command->info('Đã tạo 4 Hợp đồng đang hiệu lực và lịch thanh toán tương ứng.');

            // 5. Tạo một số hợp đồng đã hết hạn (expired) hoặc đã hủy (cancelled)
            $expiredKiosks = $kiosks->whereNotIn('id', $activeKiosks->pluck('id'))->random(2);
            foreach ($expiredKiosks as $index => $kiosk) {
                // Khách hàng số 4 và 5
                $customer = $customerModels[$index + 4];
                
                $startDate = $now->copy()->subYears(1)->startOfMonth();
                $endDate = $startDate->copy()->addMonths(6);

                $contract = Contract::create([
                    'reference_code' => 'HD-' . date('Y', strtotime('-1 year')) . '-' . str_pad($kiosk->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($customer->id, 3, '0', STR_PAD_LEFT),
                    'customer_id' => $customer->id,
                    'kiosk_id' => $kiosk->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'deposit_amount' => $kiosk->price * 2,
                    'total_amount' => $kiosk->price * 6,
                    'payment_cycle' => 'monthly',
                    'status' => 'expired',
                    'terms' => 'Hợp đồng đã kết thúc.',
                ]);

                // Tạo kỳ thanh toán (tất cả đều đã trả)
                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    $dueDate = $currentDate->copy();
                    ContractPaymentSchedule::create([
                        'contract_id' => $contract->id,
                        'due_date' => $dueDate->format('Y-m-d'),
                        'amount' => $kiosk->price,
                        'actual_amount' => $kiosk->price,
                        'status' => 'paid',
                        'paid_at' => $dueDate->copy()->addDays(2)->format('Y-m-d H:i:s'),
                        'payment_method' => 'cash',
                    ]);
                    $currentDate->addMonth();
                }
            }
            $this->command->info('Đã tạo 2 Hợp đồng cũ đã hết hạn.');

            // 6. Tạo Yêu cầu thuê (Rental Requests)
            // Lấy 2 Kiosk đang trống
            $emptyKiosks = $kiosks->where('status', 'available')->take(2);
            $i = 0;
            $requestStatuses = ['pending', 'approved'];
            foreach ($emptyKiosks as $kiosk) {
                RentalRequest::create([
                    'reference_code' => 'REQ-' . date('Ymd') . '-' . rand(1000, 9999),
                    'kiosk_id' => $kiosk->id,
                    'customer_id' => $customerModels[$i]->id,
                    'contact_name' => 'Lê Thị Trà My',
                    'contact_phone' => '09' . rand(10000000, 99999999),
                    'contact_email' => 'tramy' . rand(10,99) . '@gmail.com',
                    'business_model' => 'Bán hàng lưu niệm',
                    'desired_start' => now()->addDays(7)->format('Y-m-d'),
                    'desired_end' => now()->addDays(7)->addYears(1)->format('Y-m-d'),
                    'note' => 'Tôi muốn thuê quầy này để bán đặc sản Huế.',
                    'status' => $requestStatuses[$i],
                ]);
                $i++;
            }
            $this->command->info('Đã tạo 2 Yêu cầu thuê thử nghiệm.');

        });

        $this->command->info('Demo Data Seeding hoàn thành xuất sắc!');
    }
}
