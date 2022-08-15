<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('account', 'bank_name', 'bank_branch', 'bank_username', 'mobile', 'enabled', 'qr', 'remark')
            ->as('details')
            ->withTimestamps();;
    }

    public function scopeFilter($query, array $filters)
    {
        if (array_key_exists('enabled', $filters)) {
            $query->where('enabled', '=', $filters['enabled']);
        }

        $query->when(
            $filters['name'] ?? false,
            function ($query, $name) {
                return $query->where(function ($q) use ($name) {
                    $q->orWhere('name', 'like', '%' . $name . '%')->orWhere('chinese_name', 'like', '%' . $name . '%');
                });
            }
        );
    }

    public static function enabledPayments()
    {
        return static::where('enabled', 1)->get();
    }

    public function getNameAttribute($name)
    {
        $locale = Config::get('app.locale');
        switch ($locale) {
            case 'en':
                return $name;
                break;
            case 'zh':
                return $this->chinese_name;
                break;
            default:
                return $name;
                break;
        }
    }
}
