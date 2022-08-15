<?php

namespace App\Traits;

use App\Jobs\GenerateWallets;
use App\Mail\EmailVerified;
use App\Mail\OTPSent;
use App\Mail\PasswordReset;
use App\Mail\ResetPassword;
use App\Mail\SignedIn;
use App\Mail\VerificationLinkSent;
use App\Models\OtpAbility;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\Token;
use Carbon\Carbon;
// use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait Member
{
    public function enabledCurrencies()
    {
        return $this->payments()->where('payment_user.enabled', 1)->get();
    }

    public function getAverageConfirmTimeAttribute()
    {
        // return CarbonInterval::seconds()->cascade()->forHumans();
        return $this->sales()->with(['purchases'])->get()->map(function ($sale) {
            return $sale->purchases;
        })->collapse()->filter(function ($purchase) {
            return ($purchase->paid_at && $purchase->dealt_at);
        })->map(function ($purchase) {
            $paid_at = new Carbon($purchase->paid_at);
            $dealt_at = new Carbon($purchase->dealt_at);
            return $paid_at->diffInSeconds($dealt_at);
        })->avg();
    }

    public function getInviteCodeAttribute()
    {
        return "888X" . $this->id * 6 . "X666" . $this->id;
    }

    public function getIdentityAttribute()
    {
        $identifier = $this->identifiers()->first();
        if ($identifier)
            return $identifier->identity;
    }

    public function enabledPayments()
    {
        return $this->payments()->where('payment_user.enabled', 1)->get();
    }

    public function usablePayments()
    {
        return $this->enabledPayments()->pluck('id')->intersect(Payment::enabledPayments()->pluck('id'));
    }

    public function usableCurrencies()
    {
        return $this->enabledCurrencies()->pluck('currency_id')->unique()->intersect(Payment::enabledPayments()->pluck('currency_id'));
    }

    public function hasVerifiedEmail()
    {
        return $this->email_verified_at != null;
    }

    public function saleAmount(Token $token)
    {
        $sale = Sale::where('user_id', $this->id)->where('token_id', $token->id)->first();
        if (!$sale) return 0;
        return $sale->amount;
    }

    public function markEmailAsVerified()
    {
        $now = now();
        $this->email_verified_at = $now;
        $this->timelessSave();
        $this->otp->used_at = $now;
        $this->otp->save();
        GenerateWallets::dispatch($this)->afterCommit();
    }

    public function generateOTP(OtpAbility $otpAbility)
    {
        if ($this->otp != null && $otpAbility->name == $this->otp->ability->name && !$this->otp->hasExpired()) {
            $this->otp->expire();
        }
        $plainPassword = rand(1000, 9999);
        $this->otp()->create([
            'password' => bcrypt($plainPassword),
            'otp_ability_id' => $otpAbility->id
        ]);
        return $plainPassword;
    }

    public function sendEmailVerificationLink()
    {
        $otp = $this->generateOTP(OtpAbility::getVerfiyEmailOtpAbility());
        if ($otp) {
            Mail::to($this)->queue(new VerificationLinkSent($otp, $this->id));
            Log::channel('emails')->info("Email Verification Link is queue to send to " . $this->email);
        }
        return $otp != null;
    }

    public function sendEmailVerified()
    {
        Mail::to($this)->queue(new EmailVerified());
        Log::channel('emails')->info("Email Veriried is queue to send to " . $this->email);
    }

    public function sendOTP(OtpAbility $otpAbility)
    {
        $otp = $this->generateOTP($otpAbility);
        if ($otp) {
            Mail::to($this)->queue(new OTPSent($otp));
            Log::channel('emails')->info("Email OTP: $otpAbility->name is queue to send to " . $this->email);
        }
        return $otp != null;
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function sendPasswordResetNotification($ip)
    {
        Mail::to($this)->queue(new PasswordReset($ip));
        Log::channel('emails')->info("Email 'Password Reset' is queue to send to " . $this->email);
    }

    public function sendResetPassword(OtpAbility $otpAbility)
    {
        $otp = $this->generateOTP($otpAbility);
        if ($otp) {
            Mail::to($this)->queue(new ResetPassword($otp));
            Log::channel('emails')->info("Email 'Reset Password' is queue to send to " . $this->email);
        }
        return $otp != null;
    }

    public function sendSignedInNotification()
    {
        Mail::to($this)->queue(new SignedIn($this));
        Log::channel('emails')->info("Email 'Signed In' is queue to send to " . $this->email);
    }
}
