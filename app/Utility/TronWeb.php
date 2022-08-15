<?php

namespace App\Utility;

use App\Models\Approval;
use App\Models\CryptoWallet;
use App\Models\CryptoNetwork;
use App\Models\Token;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TronWeb
{
    public static function transferFrom(CryptoWallet $fromWallet, CryptoWallet $toWallet, $amount)
    {
        $fromBalance = static::balanceOf($fromWallet);
        $tradeFees = CryptoNetwork::where('name', 'tron')->firstOrFail()->trade_fees;
        if ($amount <= $tradeFees) return;
        if ($amount > $fromBalance) return;
        if (!$fromWallet->activated_at) static::activateTronAccount($fromWallet);
        if (!$toWallet->activated_at) static::activateTronAccount($toWallet);
        $fromAllowance = static::allowance($fromWallet);
        if ($amount > $fromAllowance) static::increaseAllowance($fromWallet, 1000000);
        $response = Http::post(env('NODE_URL') . "/transfer-from", [
            'from_address' => $fromWallet->base58_check,
            'to_address' => $toWallet->base58_check,
            'amount' => $amount - $tradeFees
        ])->throw()->object();
        Log::channel('tronweb')->info("transferFrom : " . $fromWallet->user->id . " to " . $toWallet->user->id . " amount $amount " . json_encode($response));
        return $response;
    }

    public static function chargeTradeFees(CryptoWallet $fromWallet)
    {
        $tron = CryptoNetwork::where('name', 'tron')->firstOrFail();
        $response = Http::post(env('NODE_URL') . "/transfer-from", [
            'from_address' => $fromWallet->base58_check,
            'to_address' => $tron->address,
            'amount' => $tron->trade_fees
        ])->throw()->object();
        Log::channel('tronweb')->info("chargeTradeFees : from " . $fromWallet->user->id . ". Fees $tron->trade_fees. " . json_encode($response));
        return $response;
    }

    public static function chargeWithdrawFees(CryptoWallet $fromWallet)
    {
        $tron = CryptoNetwork::where('name', 'tron')->firstOrFail();
        $fees = $tron->withdraw_fees;
        $response = Http::post(env('NODE_URL') . "/transfer-from", [
            'from_address' => $fromWallet->base58_check,
            'to_address' => $tron->address,
            'amount' => $fees
        ])->throw()->object();
        Log::channel('tronweb')->info("chargeWithdrawFees from " . $fromWallet->user->id . ". Fees $fees. " . json_encode($response));
        return $response;
    }

    public static function externalTransferFrom(CryptoWallet $fromWallet, $address, $amount)
    {
        $fromBalance = static::balanceOf($fromWallet);
        $max = $fromWallet->withdrawableAmount(Token::where('name', 'tron_usdt')->first(), $fromBalance);
        $withdrawFees = CryptoNetwork::where('name', 'tron')->firstOrFail()->withdraw_fees;
        if ($amount <= $withdrawFees) return;
        if ($amount > $max) return;
        if (!$fromWallet->activated_at) static::activateTronAccount($fromWallet);

        $fromAllowance = static::allowance($fromWallet);
        if ($amount > $fromAllowance) static::increaseAllowance($fromWallet, 1000000);
        $response = Http::post(env('NODE_URL') . "/transfer-from", [
            'from_address' => $fromWallet->base58_check,
            'to_address' => $address,
            'amount' => $amount - $withdrawFees
        ])->throw()->object();
        Log::channel('tronweb')->info("externalTransferFrom : " . $fromWallet->user->id . " to " . $address . " amount $amount " . json_encode($response));
        return $response;
    }

    public static function balanceOf(CryptoWallet $wallet)
    {
        $response = Http::post(env('NODE_URL') . "/balanceOf", [
            'address' => $wallet->base58_check
        ])->throw()->object();
        Log::channel('tronweb')->info("balanceOf : " . $wallet->base58_check . " : " . json_encode($response));
        return $response;
    }

    // public static function approve(CryptoWallet $wallet, $amount)
    // {
    //     $response = Http::post(env('NODE_URL') . "/approve", [
    //         'privateKey' => $wallet->private_key,
    //         'amount' => $amount
    //     ])->throw()->object();
    //     Log::channel('tronweb')->info("approve : " . json_encode($response));
    //     return $response;
    // }

    public static function allowance(CryptoWallet $wallet)
    {
        $response = Http::post(env('NODE_URL') . "/allowance", [
            'address' => $wallet->base58_check,
        ])->throw()->object();
        Log::channel('tronweb')->info("allowance : " . $wallet->base58_check . " : " . json_encode($response));
        return $response;
    }

    // public static function decreaseAllowance(CryptoWallet $wallet, $amount)
    // {
    //     $response = Http::post(env('NODE_URL') . "/decrease-allowance", [
    //         'privateKey' => $wallet->private_key,
    //         'amount' => $amount,
    //     ])->throw()->object();
    //     Log::channel('tronweb')->info("decreaseAllowance : " . json_encode($response));
    //     return $response;
    // }

    public static function increaseAllowance(CryptoWallet $wallet, $amount)
    {
        $response = Http::post(env('NODE_URL') . "/increase-allowance", [
            'privateKey' => $wallet->private_key,
            'amount' => $amount
        ])->throw()->object();
        Log::channel('tronweb')->info("increaseAllowance of " . $wallet->id . " : " . json_encode($response));
        if (is_string($response))
            Approval::create([
                'transaction_id' => $response,
                'crypto_wallet_id' => $wallet->id,
                'spender' => $wallet->cryptoNetwork->address,
                'token_id' => Token::where('name', 'tron_usdt')->first()->id,
                'amount' => $amount
            ]);
        return $response;
    }

    public static function activateTronAccount(CryptoWallet $wallet)
    {
        if ($wallet->activated_at != null) return;
        $response = Http::post(env('NODE_URL') . "/activate-tron-account", [
            'address' => $wallet->base58_check,
        ])->throw()->object();
        Log::channel('tronweb')->info("activateTronAccount : " . json_encode($response));
        if ($response->result) {
            $wallet->activated_at = now();
            $wallet->save();
        }
        $wallet->walletActivations()->create([
            'activation_result' => json_encode($response),
            'from' => $wallet->cryptoNetwork->address,
            'to' => $wallet->base58_check,
            'amount' => $response->transaction->raw_data->contract[0]->parameter->value->amount,
            'token_id' => $wallet->tokens()->first()->id,
            'transaction_id' => $response->txid
        ]);
        return $response;
    }

    public static function getEventByTransactionID($transactionId)
    {
        $response = Http::post(env('NODE_URL') . "/get-event-by-transaction-id", [
            'transaction_id' => $transactionId,
        ])->throw()->object();
        Log::channel('tronweb')->info("getEventByTransactionID : " . json_encode($response));
        return $response;
    }

    public static function getTransactionInfo($transactionId)
    {
        $response = Http::post(env('NODE_URL') . "/get-transaction-info", [
            'transaction_id' => $transactionId,
        ])->throw()->object();
        Log::channel('tronweb')->info("getTransactionInfo : " . json_encode($response));
        return $response;
    }

    public static function getSystemTrxBalance()
    {
        $response = Http::post(env('NODE_URL') . "/get-system-trx-balance")->throw()->object();
        Log::channel('tronweb')->info("getSystemTrxBalance : " . json_encode($response));
        return $response;
    }

    public static function getSystemResources()
    {
        $response = Http::post(env('NODE_URL') . "/get-system-resources")->throw()->object();
        Log::channel('tronweb')->info("getSystemResources : " . json_encode($response));
        return $response;
    }

    public static function freezeBalance($amount, $resource)
    {
        $response = Http::post(env('NODE_URL') . "/freeze-balance", [
            'amount' => $amount,
            'resource' => $resource
        ])->throw()->object();
        Log::channel('tronweb')->info("freezeBalance : " . json_encode($response));
        return $response;
    }

    public static function unfreezeBalance($resource)
    {
        $response = Http::post(env('NODE_URL') . "/unfreeze-balance", [
            'resource' => $resource
        ])->throw()->object();
        Log::channel('tronweb')->info("unfreezeBalance : " . json_encode($response));
        return $response;
    }

    public static function getEventsByContractAddress($contractAddress, $options)
    {
        $response = Http::post(env('NODE_URL') . "/get-events-by-contract-address", [
            'contract_address' => $contractAddress,
            'options' => $options
        ])->throw()->object();
        Log::channel('tronweb')->info("getEventsByContractAddress : " . json_encode($response));
        return $response;
    }

    public static function parseContractResult($contractResult)
    {
        $response = Http::post(env('NODE_URL') . "/parse-contract-result", [
            'contract_result' => $contractResult
        ])->throw()->object();
        Log::channel('tronweb')->info("parseContractResult : " . json_encode($response));
        return $response;
    }
}
