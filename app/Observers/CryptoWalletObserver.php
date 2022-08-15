<?php

namespace App\Observers;

use App\Models\CryptoWallet;

class CryptoWalletObserver
{
    /**
     * Handle the CryptoWallet "created" event.
     *
     * @param  \App\Models\CryptoWallet  $cryptoWallet
     * @return void
     */
    public function created(CryptoWallet $cryptoWallet)
    {
    }

    /**
     * Handle the CryptoWallet "updated" event.
     *
     * @param  \App\Models\CryptoWallet  $cryptoWallet
     * @return void
     */
    public function updated(CryptoWallet $cryptoWallet)
    {
    }

    /**
     * Handle the CryptoWallet "deleted" event.
     *
     * @param  \App\Models\CryptoWallet  $cryptoWallet
     * @return void
     */
    public function deleted(CryptoWallet $cryptoWallet)
    {
        //
    }

    /**
     * Handle the CryptoWallet "restored" event.
     *
     * @param  \App\Models\CryptoWallet  $cryptoWallet
     * @return void
     */
    public function restored(CryptoWallet $cryptoWallet)
    {
        //
    }

    /**
     * Handle the CryptoWallet "force deleted" event.
     *
     * @param  \App\Models\CryptoWallet  $cryptoWallet
     * @return void
     */
    public function forceDeleted(CryptoWallet $cryptoWallet)
    {
        //
    }
}
