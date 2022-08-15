<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Token extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function cryptoNetwork()
    {
        return $this->belongsTo(CryptoNetwork::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function wallet()
    {
        return $this->belongsToMany(CryptoWallet::class)->withTimestamps();
    }

    public function setInRedis()
    {
        Redis::set($this->name, $this);
    }
}
