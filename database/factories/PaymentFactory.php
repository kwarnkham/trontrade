<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => "Bank Transfer",
            'chinese_name' => $this->faker->unique()->name(),
            'icon' => 'payment icon',
            'country' => $this->faker->country(),
            'color' => $this->faker->colorName(),
            'enabled' => true,
            'type' => 1
        ];
    }
}
