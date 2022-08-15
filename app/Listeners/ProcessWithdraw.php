<?php

namespace App\Listeners;

use App\Events\Withdrawed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessWithdraw implements ShouldQueue
{
    use InteractsWithQueue;
    public $afterCommit = true;
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
     * @param  \App\Events\Withdrawed  $event
     * @return void
     */
    public function handle(Withdrawed $event)
    {
        $event->withdraw->processWithdraw();
    }
}
