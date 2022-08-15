<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
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

    public function networkTransaction()
    {
        return $this->morphOne(NetworkTransaction::class, 'recordable');
    }
}
