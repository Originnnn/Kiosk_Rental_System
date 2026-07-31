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

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
            'actual_amount' => $attributes['amount'] ?? 5000000,
            'payment_method' => 'bank_transfer',
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unpaid',
            'paid_at' => null,
            'actual_amount' => 0,
            'due_date' => now()->addDays(rand(5, 15)),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unpaid',
            'paid_at' => null,
            'actual_amount' => 0,
            'due_date' => now()->subDays(rand(5, 15)),
        ]);
    }
}
