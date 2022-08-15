<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'decimals' => 18,
            'unit' => 'USDT',
            'address' => 'TV4UXrfjcNw8q3J2LZNQ83nn7wTv41r3vW'
        ];
    }
}
