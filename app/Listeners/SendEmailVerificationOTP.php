<?php

namespace App\Listeners;

use App\Contracts\MustVerifyEmail;
use App\Events\UserRegistered;
use App\Models\OtpAbility;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailVerificationOTP
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        if (!$event->user->email_verified_at && $event->user instanceof MustVerifyEmail) {
            $event->user->sendOTP(OtpAbility::getInstanceByName('verify_email'));
        }
    }
}
