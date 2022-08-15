<?php

namespace App\Contracts;

use App\Models\OtpAbility;

interface MustVerifyEmail
{
    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail();

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified();

    /**
     * Send the email OTP notification.
     *
     * @return void
     */
    public function sendOTP(OtpAbility $otpAbility);

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationLink();

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification();
}
