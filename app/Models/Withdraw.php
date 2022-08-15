<?php

namespace App\Models;

use App\Jobs\RecordTransactions;
use App\Utility\TronWeb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Withdraw extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function cryptoWallet()
    {
        return $this->belongsTo(CryptoWallet::class);
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function withdrawFees()
    {
        return $this->hasMany(WithdrawFee::class);
    }

    public function networkTransaction()
    {
        return $this->morphOne(NetworkTransaction::class, 'recordable');
    }

    public function processWithdraw()
    {
        $wallet = $this->cryptoWallet;
        $transactionId = TronWeb::externalTransferFrom($wallet, $this->wallet_address, $this->amount);
        if (is_string($transactionId)) {
            $this->transaction_id = $transactionId;
            $wallet->cryptoNetwork->setBalance();
            if ($this->save()) {
                RecordTransactions::dispatch($this)->delay(now()->addSeconds(10));
                Log::channel('transactions')->info("processWithdraw :: $this");
            }
        }
    }

    public function getFromAttribute()
    {
        return $this->cryptoWallet->base58_check;
    }

    public function getToAttribute()
    {
        return $this->wallet_address;
    }
}
