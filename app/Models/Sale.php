<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPaymentsAttribute()
    {
        $user = User::find($this->user_id);
        $payments = $user->payments()->with(['currency'])
            ->where('payments.enabled', 1)
            ->where('currency_id', $this->currency_id)
            ->wherePivot('enabled', 1)->get();
        return $payments;
    }

    public function getTradingAttribute()
    {
        return $this->minPool();
    }

    public function getSoldOutAttribute()
    {
        return Purchase::where('sale_id', $this->id)->get()->reduce(function ($carry, $value) {
            if (in_array($value->status, [10, 12])) {
                return $carry += $value->amount;
            } else {
                return $carry;
            }
        }, 0);
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function sellableAmount()
    {
        return $this->user->wallet($this->token->cryptoNetwork)->balance($this->token);
    }

    public function minPool()
    {
        return Purchase::where('sale_id', $this->id)->get()->reduce(function ($carry, $value) {
            if (in_array($value->status, [1, 2, 3, 5, 7, 8, 11, 13])) {
                return $carry += $value->amount;
            } else {
                return $carry;
            }
        }, 0);
    }

    public function buyableAmount()
    {
        return $this->amount - $this->minPool();
    }

    public function network()
    {
        return $this->token->cryptoNetwork;
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['payments', 'trading', 'sold_out'];

    public function getUnpaidPurchasedAmountAttribute()
    {
        return $this->purchases->reduce(function ($carry, $purchase) {
            return $purchase->status == 1 ? $carry + $purchase->amount : $carry;
        }, 0);
    }

    public function getPaidPurchasedAmountAttribute()
    {
        return $this->purchases->reduce(function ($carry, $purchase) {
            return $purchase->status == 2 ? $carry + $purchase->amount : $carry;
        }, 0);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['currency_id'] ?? false,
            function ($query, $currency_id) {
                return $query->where('currency_id', '=', $currency_id);
            }
        );

        $query->when(
            $filters['price_sort'] ?? false,
            function ($query, $price_sort) {
                return $query->orderBy('price', $price_sort);
            }
        );

        $query->when(
            $filters['payment_currency_id'] ?? false,
            function ($query, $payment_currency_id) {
                return $query->where('currency_id', '=', $payment_currency_id);
            }
        );

        $query->when(
            $filters['user_ids'] ?? false,
            function ($query, $user_ids) {
                return $query->whereIn('user_id', $user_ids);
            }
        );

        if (array_key_exists('amount', $filters)) {
            $query->where('amount', '>=', $filters['amount'])
                ->where('max', '>=', $filters['amount'])
                ->where('min', '<=', $filters['amount']);
        }
    }
}
