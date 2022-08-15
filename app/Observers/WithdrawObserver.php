<?php

namespace App\Observers;

use App\Events\Withdrawed;
use App\Models\Withdraw;

class WithdrawObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Withdraw "created" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function created(Withdraw $withdraw)
    {
        Withdrawed::dispatch($withdraw);
    }

    /**
     * Handle the Withdraw "updated" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function updated(Withdraw $withdraw)
    {
        //
    }

    /**
     * Handle the Withdraw "deleted" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function deleted(Withdraw $withdraw)
    {
        //
    }

    /**
     * Handle the Withdraw "restored" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function restored(Withdraw $withdraw)
    {
        //
    }

    /**
     * Handle the Withdraw "force deleted" event.
     *
     * @param  \App\Models\Withdraw  $withdraw
     * @return void
     */
    public function forceDeleted(Withdraw $withdraw)
    {
        //
    }
}
