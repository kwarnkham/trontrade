<?php

namespace App\Observers;

use App\Models\CryptoNetwork;

class CryptoNetworkObserver
{
    /**
     * Handle the CryptoNetwork "created" event.
     *
     * @param  \App\Models\CryptoNetwork  $cryptoNetwork
     * @return void
     */
    public function created(CryptoNetwork $cryptoNetwork)
    {
        $cryptoNetwork->setInRedis();
    }

    /**
     * Handle the CryptoNetwork "updated" event.
     *
     * @param  \App\Models\CryptoNetwork  $cryptoNetwork
     * @return void
     */
    public function updated(CryptoNetwork $cryptoNetwork)
    {
        $cryptoNetwork->setInRedis();
    }

    /**
     * Handle the CryptoNetwork "deleted" event.
     *
     * @param  \App\Models\CryptoNetwork  $cryptoNetwork
     * @return void
     */
    public function deleted(CryptoNetwork $cryptoNetwork)
    {
    }

    /**
     * Handle the CryptoNetwork "restored" event.
     *
     * @param  \App\Models\CryptoNetwork  $cryptoNetwork
     * @return void
     */
    public function restored(CryptoNetwork $cryptoNetwork)
    {
        //
    }

    /**
     * Handle the CryptoNetwork "force deleted" event.
     *
     * @param  \App\Models\CryptoNetwork  $cryptoNetwork
     * @return void
     */
    public function forceDeleted(CryptoNetwork $cryptoNetwork)
    {
        //
    }
}
