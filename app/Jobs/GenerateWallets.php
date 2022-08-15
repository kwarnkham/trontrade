<?php

namespace App\Jobs;

use App\Models\CryptoNetwork;
use App\Models\CryptoWallet;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class GenerateWallets implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [(new WithoutOverlapping($this->user->id))->expireAfter(60)];
    }

    /**
     * The unique ID of the job.
     *
     * @return int
     */
    public function uniqueId()
    {
        return $this->user->id;
    }

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach (CryptoNetwork::all() as $cryptoNetwork) {
            $wallet = CryptoWallet::create(array_merge(
                call_user_func("\\App\\Api\\" . ucwords($cryptoNetwork->name) . '::generateAddressLocally'),
                ['crypto_network_id' => $cryptoNetwork->id, 'user_id' => $this->user->id],
            ));
            $wallet->linkTokens();
        }
    }
}
