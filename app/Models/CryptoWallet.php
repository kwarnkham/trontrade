<?php

namespace App\Models;

use App\Events\UserBalanceUpdated;
use App\Jobs\RecordSyncWallet;
use App\Mail\Withdrawed;
use App\Utility\TronWeb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CryptoWallet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['private_key'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function walletActivations()
    {
        return $this->hasMany(WalletActivation::class);
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class);
    }

    // public function networkTokens()
    // {
    //     return $this->hasManyThrough(Token::class, CryptoNetwork::class, 'id', 'crypto_network_id');
    // }

    public function balance(Token $token)
    {
        return $this->tokens()->findOrFail($token->id)->pivot->balance;
    }

    // public function increaseBalance(Token $token, $amount)
    // {
    //     $result = $this->tokens()->updateExistingPivot($token->id, [
    //         'balance' => $this->balance($token) + $amount,
    //     ]);

    //     UserBalanceUpdated::dispatch($this->refresh());

    //     return $result;
    // }

    // public function decreaseBalance(Token $token, $amount)
    // {
    //     $result = $this->tokens()->updateExistingPivot($token->id, [
    //         'balance' => $this->balance($token) - $amount,
    //     ]);

    //     UserBalanceUpdated::dispatch($this->refresh());

    //     return $result;
    // }

    public function tokens()
    {
        return $this->belongsToMany(Token::class)->withPivot(['balance'])->withTimestamps();
    }

    public function cryptoNetwork()
    {
        return $this->belongsTo(CryptoNetwork::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'private_key' => 'encrypted'
    ];

    public function syncWallet(Token $token, $record = false)
    {
        if ($token->name == 'tron_usdt') {
            $balance = TronWeb::balanceOf($this);
            $this->tokens()->updateExistingPivot($token->id, [
                'balance' => $balance,
            ]);

            RecordSyncWallet::dispatchIf($record, $this, $token);

            return $balance;
        }
    }

    public static function validateExternal($address, Token $token)
    {
        $wallets = CryptoWallet::where('crypto_network_id', $token->cryptoNetwork->id)->get();
        $systemWalletAddress = $token->cryptoNetwork->address;
        if ($address == $systemWalletAddress) return false;
        return !$wallets->contains(function ($wallet, $key) use ($address) {
            $contain = false;
            if ($wallet->base58_check == $address) $contain = true;
            else if ($wallet->hex_address == $address) $contain = true;
            return $contain;
        });
    }

    public function linkTokens()
    {
        $tokens = $this->cryptoNetwork->tokens;
        if (count($this->tokens) < count($tokens)) {
            foreach ($tokens as $token) {
                if (!$this->tokens->contains(function ($value) use ($token) {
                    return $value->name == $token->name;
                })) {
                    $this->tokens()->attach($token->id);
                }
            }
        }
    }

    public function sendWithdrawedNotification($amount, Token $token)
    {
        UserBalanceUpdated::dispatch($this->balance($token), $token->id, $this->user->id);
        if ($this->user->email) {
            Mail::to($this->user)->queue(new Withdrawed($amount, $this));
            Log::channel('emails')->info("Email 'Withdrawed' is queue to send to " . $this->user->email);
        }
    }

    public function withdrawableAmount(Token $token, $balance)
    {
        return $balance - $this->user->sales()->where('token_id', $token->id)->get()->reduce(function ($carry, $sale) {
            return $carry + $sale->amount;
        }, 0);
    }
}
