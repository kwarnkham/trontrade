<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawFee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function withdraw()
    {
        return $this->belongsTo(Withdraw::class);
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function networkTransaction()
    {
        return $this->morphOne(NetworkTransaction::class, 'recordable');
    }

    public function getFromAttribute()
    {
        return $this->withdraw->cryptoWallet->base58_check;
    }

    public function getToAttribute()
    {
        return $this->withdraw->wallet_address;
    }

    public function getAmountAttribute()
    {
        return $this->withdraw->withdraw_fees;
    }
}
