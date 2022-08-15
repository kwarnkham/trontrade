<?php

namespace App\Observers;

use App\Jobs\RecordTransactions;
use App\Models\Approval;

class ApprovalObserver
{
    /**
     * Handle the Approval "created" event.
     *
     * @param  \App\Models\Approval  $approval
     * @return void
     */
    public function created(Approval $approval)
    {
        RecordTransactions::dispatch($approval)->delay(now()->addSeconds(10));
    }
}
