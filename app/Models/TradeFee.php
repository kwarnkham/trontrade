<?php

namespace App\Models;

use App\Jobs\RecordTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeFee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
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
