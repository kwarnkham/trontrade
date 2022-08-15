<?php

namespace App\Observers;

use App\Events\SaleUpdated;
use App\Models\Purchase;

class PurchaseObserver
{
    /**
     * Handle the Purchase "created" event.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return void
     */
    public function created(Purchase $purchase)
    {
        $purchase->sendPurchaseCreatedNotification();
        SaleUpdated::dispatch($purchase->sale);
    }

    /**
     * Handle the Purchase "updated" event.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return void
     */
    public function updated(Purchase $purchase)
    {
        if ($purchase->status == 2) $purchase->sendPurchasePaidNotification();
        // if ($purchase->status == 3 && !$purchase->transaction_id) $purchase->sendPurchaseDealtNotification();
        if ($purchase->status == 4) $purchase->sendPurchaseCancelledNotification();
        if ($purchase->status == 5) $purchase->sendPurchaseRejectedNotification();
        $purchase->processDealtTransaction();
    }

    /**
     * 
     * Handle the Purchase "deleted" event.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return void
     */
    public function deleted(Purchase $purchase)
    {
        //
    }

    /**
     * Handle the Purchase "restored" event.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return void
     */
    public function restored(Purchase $purchase)
    {
        //
    }

    /**
     * Handle the Purchase "force deleted" event.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return void
     */
    public function forceDeleted(Purchase $purchase)
    {
        //
    }
}
