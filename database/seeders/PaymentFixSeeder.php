<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;
use Carbon\Carbon;

class PaymentFixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Delete all existing payment records to fix the data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        if (DB::getSchemaBuilder()->hasTable('payment_transactions')) {
            DB::table('payment_transactions')->truncate();
        }
        DB::table('contract_payment_schedules')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Fetch all contracts to generate logical payments
        $contracts = Contract::with('kiosk')->get();

        foreach ($contracts as $contract) {
            // Determine monthly amount
            $amount = $contract->kiosk ? $contract->kiosk->price : 5000000;
            
            if ($contract->total_amount > 0) {
                // Approximate monthly amount from total_amount if available
                // Assuming most contracts are 12 months for dummy data
                $amount = $contract->total_amount / 12;
            }

            // Generate exactly 1 payment per month for the last 6 months
            for ($i = 0; $i < 6; $i++) {
                $billingDate = Carbon::now()->startOfMonth()->subMonths($i);
                
                // Status logic
                if ($i === 0) {
                    // Current month: 50% paid, 50% pending
                    $status = rand(0, 1) === 0 ? 'paid' : 'pending';
                } else {
                    // Past months: 85% paid, 15% overdue
                    $status = rand(1, 100) <= 85 ? 'paid' : 'overdue';
                }
                
                $isPaid = $status === 'paid';
                
                ContractPaymentSchedule::create([
                    'contract_id' => $contract->id,
                    'due_date' => $billingDate->copy()->addDays(5), // Due on the 5th of each month
                    'amount' => $amount,
                    'actual_amount' => $isPaid ? $amount : 0,
                    'status' => $status,
                    'paid_at' => $isPaid ? $billingDate->copy()->addDays(rand(1, 5)) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('Đã xóa dữ liệu thanh toán cũ và sinh lại Lịch sử thanh toán mới (mỗi tháng 1 kỳ) thành công!');
    }
}
