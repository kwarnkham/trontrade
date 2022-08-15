<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class OneTimePassword extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expired_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($otp) {
            if ($otp->expired_at == null) {
                $otp->expired_at = now()->addMinutes(15);
                $otp->save();
            }
        });
    }

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ability()
    {
        return $this->belongsTo(OtpAbility::class, 'otp_ability_id', 'id');
    }

    public function verify(string $code, OtpAbility $otpAbilityToCheckAgainst)
    {
        if ($this->ability->name != $otpAbilityToCheckAgainst->name) {
            return;
        } else if (!$this->hasExpired() && Hash::check($code, $this->password) && !$this->used_at) {
            $this->used_at = now();
            return $this->save();
        }
    }

    public function hasExpired()
    {
        return now()->greaterThanOrEqualTo($this->expired_at);
    }

    public function expire()
    {
        $this->expired_at = now();
        $this->save();
        return $this;
    }


    public function markAsUsed()
    {
        $this->used_at = now();
        return $this->save();
    }
}
