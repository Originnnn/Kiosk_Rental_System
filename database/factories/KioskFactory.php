<?php

namespace Database\Factories;

use App\Models\Kiosk;
use Illuminate\Database\Eloquent\Factories\Factory;

class KioskFactory extends Factory
{
    protected $model = Kiosk::class;

    public function definition(): array
    {
        // 5 triệu - 20 triệu
        $price = $this->faker->numberBetween(5000000, 20000000);
        
        return [
            'code' => 'K-' . $this->faker->unique()->numberBetween(100, 9999),
            'name' => 'Quầy ' . $this->faker->words(2, true),
            'area' => $this->faker->randomFloat(2, 10, 100),
            'price' => $price,
            'description' => $this->faker->sentence(),
            // 80% rented, 10% available, 10% reserved
            'status' => $this->faker->randomElement(array_merge(
                array_fill(0, 80, 'rented'),
                array_fill(0, 10, 'available'),
                array_fill(0, 10, 'reserved')
            )),
        ];
    }
}
