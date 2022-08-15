<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpAbility extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function OneTimePasswords()
    {
        return $this->hasMany(OneTimePassword::class);
    }

    public static function getVerfiyEmailOtpAbility()
    {
        return static::where('name', 'verify_email')->firstOrFail();
    }

    public static function getLoginOtpAbility()
    {
        return static::where('name', 'login')->firstOrFail();
    }

    public static function getWithdrawOtpAbility()
    {
        return static::where('name', 'withdraw')->firstOrFail();
    }

    public static function getResetPasswordOtpAbility()
    {
        return static::where('name', 'reset_password')->firstOrFail();
    }

    public static function getInstanceByName(string $name)
    {
        return static::where('name', $name)->firstOrFail();
    }
}
