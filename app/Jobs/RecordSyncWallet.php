<?php

namespace App\Jobs;

use App\Api\Tron;
use App\Models\CryptoWallet;
use App\Models\Deposit;
use App\Models\NetworkTransaction;
use App\Models\Token;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordSyncWallet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $wallet;
    protected $token;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CryptoWallet $wallet, Token $token)
    {
        $this->wallet = $wallet;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $network = $this->token->cryptoNetwork;
        if ($network->name == 'tron') {
            $transactions = collect(Tron::getTRC20TransactionInfoByAccountAddress(
                $this->wallet->base58_check,
                [
                    'limit' => 200,
                    'only_to' => true,
                    'min_timestamp' => now()->subDays(28)->timestamp * 1000,
                    'contract_address' => $this->token->address
                ]
            )->data)->reject(function ($transaction) use ($network) {
                return in_array($transaction->from, CryptoWallet::whereBelongsTo($network)->pluck('base58_check')->toArray());
            })->reject(function ($transaction) use ($network) {
                return in_array($transaction->transaction_id, Deposit::whereBelongsTo($this->token)->pluck('transaction_id')->toArray());
            });
        }


        foreach ($transactions as $transaction) {
            $amount = $transaction->value / pow(10, $transaction->token_info->decimals);
            NetworkTransaction::recordDeposit(Deposit::create([
                'transaction_id' => $transaction->transaction_id,
                'crypto_wallet_id' => $this->wallet->id,
                'token_id' => $this->token->id,
                'amount' => $amount,
                'transaction' => json_encode($transaction),
                'from' => $transaction->from,
                'to' => $transaction->to
            ]));
        }
    }
}
