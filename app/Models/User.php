<?php

namespace App\Models;

use App\Contracts\MustVerifyEmail;
use App\Traits\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Translation\HasLocalePreference;

class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    use HasApiTokens, HasFactory, Member;

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        return $this->setting ? $this->setting->locale : 'en';
    }

    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class)->withPivot('account', 'bank_name', 'bank_branch', 'bank_username', 'mobile', 'enabled', 'qr', 'remark')
            ->as('details')
            ->withTimestamps();
    }

    public function referrees()
    {
        return $this->hasMany(User::class, 'referrer_id', 'id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function cryptoWallets()
    {
        return $this->hasMany(CryptoWallet::class);
    }

    public function wallet(CryptoNetwork $cryptoNetwork)
    {
        return $this->hasMany(CryptoWallet::class)->whereBelongsTo($cryptoNetwork)->first();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function identifiers()
    {
        return $this->belongsToMany(Identifier::class)
            ->withPivot('first_name', 'middle_name', 'last_name', 'number', 'sub_number', 'images', 'status', 'confirmed_at')
            ->as('identity')
            ->withTimestamps();
    }

    public function passwordChanges()
    {
        return $this->hasMany(PasswordChange::class);
    }

    public function latestPasswordChange()
    {
        return $this->hasOne(PasswordChange::class)->latestOfMany();
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id', 'id');
    }

    public function oneTimePasswords()
    {
        return $this->hasMany(OneTimePassword::class);
    }

    /**
     * Get the user's most recent one time password.
     */
    public function otp()
    {
        return $this->hasOne(OneTimePassword::class)->latestOfMany();
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['average_confirm_time', 'invite_code', 'identity'];


    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'google2fa_secret',
        'otp'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
        'google2fa_secret_verified_at' => 'datetime',
        'google2fa_secret' => 'encrypted'
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['wallet'] ?? false,
            function ($query, $wallet) {
                return $query
                    ->join('crypto_wallets', 'crypto_wallets.user_id', '=', 'users.id')
                    ->select('users.*')
                    ->where('crypto_wallets.base58_check', $wallet);
            }
        );

        $query->when(
            $filters['agent'] ?? false,
            function ($query, $agent) {
                $agent = Agent::where('name', $agent)->first();
                return $agent ? $query->whereBelongsTo($agent) : $query->where('users.id', 0);
            }
        );

        $query->when(
            $filters['email'] ?? false,
            function ($query, $email) {
                return $query->where('email', '=', $email);
            }
        );

        $query->when(
            $filters['email_verified'] ?? false,
            function ($query) {
                return $query->where('email_verified_at', '!=', null);
            }
        );

        $query->when(
            $filters['banned'] ?? false,
            function ($query) {
                return $query->where('banned_at', '!=', null);
            }
        );

        if (array_key_exists('banned', $filters)) {
            if ($filters['banned'] == 0) $query->where('banned_at', null);
            if ($filters['banned'] == 1) $query->where('banned_at', '!=', null);
        }
    }

    /**
     * Get user model by email
     *
     * @param string $email
     * @return \App\Models\User
     */
    public static function getUserByEmail(string $email)
    {
        return static::where('email', $email)->firstOrFail();
    }


    public static function getIdByInviterId($referrerId)
    {
        $temp = substr(substr($referrerId, 4), 0, strpos(substr($referrerId, 4), "X"));
        if (!$temp) return;
        return $temp / 6;
    }

    public function timelessSave()
    {
        $this->timestamps = false;
        $result = $this->save();
        $this->timestamps = true;
        return $result;
    }

    public function isAdmin()
    {
        return $this->roles->contains(function ($role) {
            return $role->id == 1;
        });
    }
}
