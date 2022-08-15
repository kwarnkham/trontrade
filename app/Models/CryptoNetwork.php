<?php

namespace App\Models;

use App\Mail\LowEnergy;
use App\Mail\LowTrxBalance;
use App\Utility\TronWeb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class CryptoNetwork extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['api_url', 'api_key', 'private_key', 'balance', 'resources'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'private_key' => 'encrypted',
        'api_key' => 'encrypted'
    ];


    public function cryptoWallets()
    {
        return $this->hasMany(CryptoWallet::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    public function setInRedis()
    {
        $private_key = $this->private_key;
        $api_url = $this->api_url;
        $api_key = $this->api_key;
        $tronNetwork = $this->toArray();
        $tronNetwork['private_key'] = $private_key;
        $tronNetwork['api_key'] = $api_key;
        $tronNetwork['api_url'] = $api_url;
        Redis::set($this->name . '_network', json_encode($tronNetwork));
    }

    public function setBalance()
    {
        if ($this->name == 'tron') {
            $this->balance = TronWeb::getSystemTrxBalance();

            Log::channel('transactions')->info("System balance is " . $this->balance);
            if ($this->balance < 500) {
                Mail::to(env('NOTICE_EMAIL'))->queue(new LowTrxBalance($this->balance));
                Log::channel('emails')->info("Email LowTrxBalance is queue to send to " . env("NOTICE_EMAIL"));
            }
            $resources = TronWeb::getSystemResources();
            if (isset($resources->EnergyLimit) && $resources->EnergyLimit < 500000) {
                Mail::to(env('NOTICE_EMAIL'))->queue(new LowEnergy($resources->EnergyLimit));
                Log::channel('emails')->info("Email LowEnergy is queue to send to " . env("NOTICE_EMAIL"));
            }
            $this->resources = json_encode($resources);
            Cache::forever('tronNetwork', $this);
            return $this->save();
        }
    }
}
