<?php

namespace App\Models;

use App\Jobs\RecordTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($approval) {
            RecordTransactions::dispatch($approval)->delay(now()->addSeconds(10));
        });
    }

    protected $guarded = ['id'];

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function getFromAttribute()
    {
        return $this->cryptoWallet->base58_check;
    }

    public function getToAttribute()
    {
        return $this->spender;
    }

    public function cryptoWallet()
    {
        return $this->belongsTo(CryptoWallet::class);
    }

    public function networkTransaction()
    {
        return $this->morphOne(NetworkTransaction::class, 'recordable');
    }
}
