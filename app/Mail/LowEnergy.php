<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowEnergy extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $energy;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($energy)
    {
        $this->energy = $energy;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.lowEnergy');
    }
}
