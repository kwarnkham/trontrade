<?php

namespace App\Providers;

use App\Models\CryptoNetwork;
use App\Models\Token;
use Illuminate\Support\ServiceProvider;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(StorageClient::class, function () {
            return new StorageClient([
                'keyFile' => json_decode(file_get_contents(base_path('service-account-file.json')), true)
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // if (!Redis::exists('hex_addresses')) {
        //     $hex_addresses = CryptoWallet::select('hex_address')->setEagerLoads([])->pluck('hex_address')->unique();
        //     foreach ($hex_addresses as $address) {
        //         Redis::lpush('hex_addresses', $address);
        //     }
        // }

        // if (!Redis::exists('tron_api_key') && env("TRON_API_KEY")) {
        //     Redis::set('tron_api_key', json_encode(env("TRON_API_KEY")));
        // }

        // if (!Redis::exists('selected_tron_api_key')) {
        //     Redis::set('selected_tron_api_key', 0);
        // }
        if (Schema::hasTable('crypto_networks') && Schema::hasTable('tokens')) {
            if (!Redis::exists('tron_network')) {
                $tron = CryptoNetwork::where('name', 'tron')->first();
                if ($tron) $tron->setInRedis();
            }

            if (!Redis::exists('tron_usdt')) {
                $usdt = Token::where('name', 'tron_usdt')->first();
                if ($usdt) $usdt->setInRedis();
            }
        }
    }
}
