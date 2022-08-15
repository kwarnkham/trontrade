<?php

namespace App\Mail;

use App;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VerificationLinkSent extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password, $userId)
    {
        $this->url = URL::temporarySignedRoute(
            'verifyEmail',
            now()->addMinutes(30),
            ['user' => $userId, 'password' => $password]
        );
        $user = User::find($userId);
        if ($user)
            App::setLocale($user->preferredLocale());
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verification');
    }
}
