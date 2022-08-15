<?php

namespace App\Models;

use App\Jobs\RecordTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class WalletActivation extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($walletActivation) {
            if ($walletActivation->cryptoWallet->activated_at && $walletActivation->activation_result) {
                RecordTransactions::dispatch($walletActivation)->delay(now()->addSeconds(10));
            }
        });
    }

    protected $guarded = ['id'];

    public function cryptoWallet()
    {
        return $this->belongsTo(CryptoWallet::class);
    }

    public function networkTransaction()
    {
        return $this->morphOne(NetworkTransaction::class, 'recordable');
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }
}
