<?php

namespace App\Jobs;

use App\Models\Approval;
use App\Models\Deposit;
use App\Models\NetworkTransaction;
use App\Models\Purchase;
use App\Models\TradeFee;
use App\Models\WalletActivation;
use App\Models\Withdraw;
use App\Models\WithdrawFee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class RecordTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $model;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->model instanceof Purchase) {
            NetworkTransaction::recordTrade($this->model);
        } else if ($this->model instanceof TradeFee) {
            NetworkTransaction::recordTradeFee($this->model->purchase);
        } else if ($this->model instanceof Withdraw) {
            NetworkTransaction::recordWithdraw($this->model);
        } else if ($this->model instanceof WithdrawFee) {
            NetworkTransaction::recordWithdrawFee($this->model->withdraw);
        } else if ($this->model instanceof Approval) {
            NetworkTransaction::recordApproval($this->model);
        } else if ($this->model instanceof WalletActivation) {
            NetworkTransaction::recordWalletActivation($this->model);
        } else if ($this->model instanceof Deposit) {
            NetworkTransaction::recordDeposit($this->model);
        }
    }
}
