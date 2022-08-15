<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\UserRegistered;
use App\Events\Withdrawed;
use App\Listeners\ProcessWithdraw;
use App\Listeners\SendEmailVerificationOTP;
use App\Listeners\SendEmailVerificationLink;
use App\Models\Approval;
use App\Models\CryptoNetwork;
use App\Models\CryptoWallet;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Token;
use App\Models\Withdraw;
use App\Observers\ApprovalObserver;
use App\Observers\CryptoNetworkObserver;
use App\Observers\CryptoWalletObserver;
use App\Observers\PurchaseObserver;
use App\Observers\SaleObserver;
use App\Observers\TokenObserver;
use App\Observers\WithdrawObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserRegistered::class => [
            // SendEmailVerificationOTP::class,
            SendEmailVerificationLink::class
        ],
        Withdrawed::class => [
            ProcessWithdraw::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Sale::observe(SaleObserver::class);
        Purchase::observe(PurchaseObserver::class);
        CryptoNetwork::observe(CryptoNetworkObserver::class);
        Token::observe(TokenObserver::class);
        Withdraw::observe(WithdrawObserver::class);
        Approval::observe(ApprovalObserver::class);
        // CryptoWallet::observe(CryptoWalletObserver::class);
    }
}
