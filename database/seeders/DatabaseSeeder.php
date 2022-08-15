<?php

namespace Database\Seeders;

use App\Models\CryptoNetwork;
use App\Models\Currency;
use App\Models\Identifier;
use App\Models\OtpAbility;
use App\Models\Role;
use App\Models\Token;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $otpAbilities = ['verify_email', 'reset_password', 'change_password', 'withdraw', 'login'];
        foreach ($otpAbilities as $otpAbility) {
            OtpAbility::factory()->create(['name' => $otpAbility]);
        }
        User::factory(['email' => 'liu66375@gmail.com'])->hasAttached(Role::factory())->create();
        Identifier::factory()->create();
        Currency::factory()->create();

        if (env('APP_ENV') == 'production') {
            $cryptoNetwork = CryptoNetwork::factory()->create([
                'name' => 'tron',
                'api_url' => 'https://api.trongrid.io',
                "api_key" => '',
            ]);
            Token::factory()->for($cryptoNetwork)->create([
                'name' => 'tron_usdt',
                'display_name' => 'USDT-TRC20',
                'decimals' => 6,
                'unit' => 'USDT',
                'address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t'
            ]);
        } else {
            $cryptoNetwork = CryptoNetwork::factory()->create([
                'name' => 'tron',
                'api_url' => 'https://api.shasta.trongrid.io',
                "address" => "TN7xd5t5CeKmiWnk3EtRa8NcRswP1gR34r",
                "private_key" => "",
            ]);
            Token::factory()->for($cryptoNetwork)->create([
                'name' => 'tron_usdt',
                'display_name' => 'USDT-TRC20',
                'decimals' => 18,
                'unit' => 'USDT',
                'address' => 'TV4UXrfjcNw8q3J2LZNQ83nn7wTv41r3vW'
            ]);
        }
    }
}
