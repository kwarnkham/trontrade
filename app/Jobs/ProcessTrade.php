<?php

namespace App\Jobs;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTrade implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $purchase;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }
    public $tries = 1;

    public $uniqueFor = 30;

    public function uniqueId()
    {
        return $this->purchase->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $result = $this->purchase->transferToken();

        if ($result) {
            RecordTransactions::dispatch($this->purchase)->delay(now()->addSeconds(10));
            Log::channel('transactions')->info("Sent token to buyer.Purchase :: $result");
        }
    }
}
