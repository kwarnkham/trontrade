<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount' => 1,
            'trade_fees' => 1,
            'sale_id' => 1,
            'user_id' => 3,
            'unit_price' => 1
        ];
    }
}
