<?php

namespace App\Models;

use App\Api\Tron;
use App\Jobs\RecordTransactions;
use App\Utility\TronWeb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class NetworkTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function recordable()
    {
        return $this->morphTo();
    }

    public function cryptoNetwork()
    {
        return $this->belongsTo(CryptoNetwork::class);
    }

    public function getTypeAttribute($attribute)
    {
        if ($attribute == 'Purchase') return "Trade";
        return $attribute;
    }

    public static function sync(Carbon $timeScope = null)
    {
        if (!$timeScope) $timeScope = now()->subDays(2);
        $count = [
            'purchases' => 0,
            'tradeFees' => 0,
            'walletActivations' => 0,
            'deposits' => 0,
            'approvals' => 0,
            'withdraws' => 0,
            'withdrawFees' => 0
        ];

        $purchases = Purchase::where('updated_at', '>', $timeScope)
            ->whereNotNull('transaction_id')
            ->whereDoesntHave('networkTransaction')->get();
        foreach ($purchases as $purchase) {
            RecordTransactions::dispatch($purchase);
            $count['purchases']++;
        }

        $tradeFees = TradeFee::where('updated_at', '>', $timeScope)->whereDoesntHave('networkTransaction')->get();
        foreach ($tradeFees as $tradeFee) {
            RecordTransactions::dispatch($tradeFee);
            $count['tradeFees']++;
        }

        $withraws = Withdraw::where('updated_at', '>', $timeScope)->whereNotNull('transaction_id')->whereDoesntHave('networkTransaction')->get();
        foreach ($withraws as $withraw) {
            RecordTransactions::dispatch($withraw);
            $count['withdraws']++;
        }

        $withdrawFees = WithdrawFee::where('updated_at', '>', $timeScope)->whereDoesntHave('networkTransaction')->get();
        foreach ($withdrawFees as $withdrawFee) {
            RecordTransactions::dispatch($withdrawFee);
            $count['withdrawFees']++;
        }

        $approvals = Approval::where('updated_at', '>', $timeScope)->whereDoesntHave('networkTransaction')->get();
        foreach ($approvals as $approval) {
            RecordTransactions::dispatch($approval);
            $count['approvals']++;
        }

        $walletActivations = WalletActivation::where('updated_at', '>', $timeScope)->whereDoesntHave('networkTransaction')->get();
        foreach ($walletActivations as $walletActivation) {
            RecordTransactions::dispatch($walletActivation);
            $count['walletActivations']++;
        }

        $deposits = Deposit::where('updated_at', '>', $timeScope)->whereDoesntHave('networkTransaction')->get();
        foreach ($deposits as $deposit) {
            RecordTransactions::dispatch($deposit);
            $count['deposits']++;
        }

        return $count;
    }

    public function scopeFilter($query, array $filters)
    {

        $query->when(
            $filters['start_time'] ?? false,
            function ($query, $start_time) {
                return $query->where('created_at', '>=', $start_time);
            }
        );

        $query->when(
            $filters['end_time'] ?? false,
            function ($query, $end_time) {
                return $query->where('created_at', '<=', $end_time);
            }
        );

        $query->when(
            $filters['agent_id'] ?? false,
            function ($query, $agent_id) {
                $agent = Agent::find($agent_id);
                $wallets = $agent->users->load(['cryptoWallets'])->map(function ($user) {
                    return $user->cryptoWallets;
                })->collapse()->map(function ($wallet) {
                    return $wallet->base58_check;
                });
                return $query->where(function ($q) use ($wallets) {
                    $q->orWhereIn('to', $wallets);
                    $q->orWhereIn('from', $wallets);
                });
            }
        );

        $query->when(
            $filters['transaction_id'] ?? false,
            function ($query, $transaction_id) {
                return $query->where('transaction_id', '=', $transaction_id);
            }
        );

        $query->when(
            $filters['from'] ?? false,
            function ($query, $from) {
                return $query->where('from', '=', $from);
            }
        );

        $query->when(
            $filters['to'] ?? false,
            function ($query, $to) {
                return $query->where('to', '=', $to);
            }
        );

        $query->when(
            $filters['status'] ?? false,
            function ($query, $status) {
                return $query->where('status', '=', $status);
            }
        );

        $query->when(
            $filters['type'] ?? false,
            function ($query, $type) {
                if ($type == 'Trade') $type = 'Purchase';
                return $query->where('type', '=', $type);
            }
        );
    }

    public static function saveTransaction(Model $model, $type)
    {
        $network = $model->token->cryptoNetwork;
        $networkTransaction = NetworkTransaction::where('transaction_id', $model->transaction_id)->whereBelongsTo($network)->first();
        if ($networkTransaction) return $networkTransaction;
        sleep(5);
        $transaction = Tron::GetTransactionInfoById($model->transaction_id);
        Log::channel('transactions')->info("Record $type: " . json_encode($transaction));

        if (!property_exists($transaction, "id")) return;
        $result = null;
        if (property_exists($transaction->receipt, 'result') && $transaction->receipt->result != "SUCCESS" && $transaction->contractResult[0] != "") {
            $result = TronWeb::parseContractResult($transaction->contractResult[0]);
        }
        $amount = $model->amount;
        if ($model instanceof Withdraw) $amount = $model->amount - $model->withdraw_fees;
        if ($model instanceof Purchase) $amount = $model->amount - $model->token->cryptoNetwork->trade_fees;
        $networkTransaction = NetworkTransaction::where('transaction_id', $transaction->id)->first();
        if (!$networkTransaction)
            $networkTransaction = NetworkTransaction::create([
                'transaction_id' => $transaction->id,
                'block_number' => $transaction->blockNumber,
                'block_timestamp' => $transaction->blockTimeStamp,
                'type' => class_basename($type),
                'from' => $model->from,
                'to' => $model->to,
                'token_id' => $model->token_id,
                'amount' => $amount,
                'fees' => $transaction->fee ?? 0,
                'receipt' => json_encode($transaction->receipt),
                'status' => $transaction->result ?? $transaction->receipt->result ?? "SUCCESS",
                'contract_result' => $result,
                'crypto_network_id' => $network->id,
                'recordable_id' => $model->id,
                'recordable_type' => $type
            ]);
        return $networkTransaction;
    }

    public static function recordTradeFee(Purchase $purchase)
    {
        $network = $purchase->sale->network();
        $tradeFee = $purchase->tradeFees()->whereHas('networkTransaction')->first();
        if (!$tradeFee) {
            if (!$purchase->tradeFees->count()) {
                $tradeFeesTransactionId = TronWeb::chargeTradeFees($purchase->sale->user->wallet($network));
                if (!is_string($tradeFeesTransactionId)) return;
                $tradeFee = TradeFee::create([
                    'purchase_id' => $purchase->id,
                    'amount' => $purchase->token->cryptoNetwork->trade_fees,
                    'transaction_id' => $tradeFeesTransactionId,
                    'from' => $purchase->from,
                    'to' => $purchase->token->cryptoNetwork->address,
                    'token_id' => $purchase->token_id
                ]);
            } else {
                $tradeFee = $purchase->tradeFees()->whereDoesntHave('networkTransaction')->first();
            }
        } else {
            $tradeFeesTransactionId = $tradeFee->transaction_id;
        }

        static::saveTransaction(
            $tradeFee,
            TradeFee::class
        );
    }

    public static function recordTrade(Purchase $purchase)
    {
        if (!$purchase->transaction_id) return;
        $network = $purchase->sale->network();
        $buyerWallet = $purchase->user->wallet($network);
        $saleWallet = $purchase->sale->user->wallet($network);

        $networkTransaction = static::saveTransaction(
            $purchase,
            Purchase::class
        );


        if ($networkTransaction->status == "SUCCESS") {
            static::recordTradeFee($purchase);
            if (!$purchase->confirmed_at) {
                $saleWallet->syncWallet($purchase->sale->token);
                $buyerWallet->syncWallet($purchase->sale->token);
                $purchase->sendPurchaseConfirmedNotification();
                $purchase->confirmed_at = now();
                $purchase->status = 10;
                $purchase->sale->amount -= $purchase->amount;
                $purchase->sale->save();
                $purchase->save();
                $network->setBalance();
            }
        } else if ($networkTransaction->status != 'SUCCESS' && !in_array($purchase->status, [11, 13])) {
            $purchase->status = 11;
            $purchase->save();
        }
    }

    public static function recordWithdrawFee(Withdraw $withdraw)
    {
        $wallet = $withdraw->cryptoWallet;
        $withdrawFee = $withdraw->withdrawFees()->whereHas('networkTransaction')->first();
        if (!$withdrawFee) {
            if (!$withdraw->withdrawFees->count()) {
                $withdrawFeesTransactionId = TronWeb::chargeWithdrawFees($wallet);
                if (!is_string($withdrawFeesTransactionId)) return;
                $withdrawFee = WithdrawFee::create([
                    'withdraw_id' => $withdraw->id,
                    'token_id' => $withdraw->token_id,
                    'transaction_id' => $withdrawFeesTransactionId
                ]);
            } else {
                $withdrawFee = $withdraw->withdrawFees()->whereDoesntHave('networkTransaction')->first();
            }
        } else {
            $withdrawFeesTransactionId = $withdrawFee->transaction_id;
        }

        static::saveTransaction(
            $withdrawFee,
            WithdrawFee::class
        );
    }


    public static function recordWithdraw(Withdraw $withdraw)
    {
        if (!$withdraw->transaction_id) return;
        $wallet = $withdraw->cryptoWallet;

        $networkTransaction = static::saveTransaction(
            $withdraw,
            Withdraw::class
        );

        if ($networkTransaction->status != "SUCCESS") return;
        static::recordWithdrawFee($withdraw);

        if (!$withdraw->confirmed_at) {
            $wallet->syncWallet($withdraw->token);
            $withdraw->confirmed_at = now();
            $withdraw->save();
            $wallet->cryptoNetwork->setBalance();
            $wallet->sendWithdrawedNotification($withdraw->amount, $withdraw->token);
        }
    }

    public static function recordDeposit(Deposit $deposit)
    {
        static::saveTransaction($deposit, Deposit::class);
    }

    public static function recordApproval(Approval $approval)
    {
        if (!$approval->transaction_id) return;
        static::saveTransaction(
            $approval,
            Approval::class
        );
    }

    public static function recordWalletActivation(WalletActivation $walletActivation)
    {
        if (!$walletActivation->activation_result) return;
        static::saveTransaction($walletActivation, WalletActivation::class);
    }
}
