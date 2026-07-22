<?php

namespace Database\Factories;

use App\Models\ContractPaymentSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ContractPaymentScheduleFactory extends Factory
{
    protected $model = ContractPaymentSchedule::class;

    public function definition(): array
    {
        return [
            'due_date' => now(),
            'amount' => 5000000,
            'actual_amount' => 5000000,
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'bank_transfer',
        ];
    }
}
