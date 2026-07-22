<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $startDate = Carbon::create(2024, 1, 1)->addDays($this->faker->numberBetween(0, 900)); // up to mid 2026
        $durationMonths = $this->faker->numberBetween(6, 36);
        $endDate = (clone $startDate)->addMonths($durationMonths);

        return [
            'reference_code' => 'HD-' . $this->faker->unique()->numberBetween(1000, 99999),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'deposit_amount' => $this->faker->numberBetween(10000000, 50000000),
            'total_amount' => $this->faker->numberBetween(100000000, 500000000),
            'payment_cycle' => $this->faker->randomElement([1, 3, 6, 12]),
            'terms' => 'Tiêu chuẩn',
            'status' => 'active',
        ];
    }
}
