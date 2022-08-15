<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CryptoWalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'private_key' => 'private_key',
            'base58_check' => 'base58_check',
            'hex_address' => 'hex_address',
            'crypto_network_id' => 1,
            'user_id' => 1
        ];
    }
}
