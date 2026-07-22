<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => '0' . $this->faker->numerify('#########'), // 10 số
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'id_card_number' => $this->faker->unique()->numerify('00109#######'), // 12 số CCCD
            'status' => 'active',
        ];
    }
}
