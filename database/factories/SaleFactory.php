<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount' => '10',
            'token_id' => 1,
            'currency_id' => 1,
            'user_id' => 2
        ];
    }
}
