<?php

namespace App\Http\Controllers;

use App\Api\Tron;
use App\Constants\ResponseStatus;
use App\Models\CryptoWallet;
use App\Models\OtpAbility;
use App\Models\Token;
use App\Models\Withdraw;
use Google2FA;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function store(Request $request, Token $token)
    {
        $passwordChange = $request->user()->latestPasswordChange;
        if ($passwordChange && $passwordChange->created_at->diffInHours(now()) <= 24) {
            abort(ResponseStatus::BAD_REQUEST, __("messages.You can only withdraw after 24 hours of changing password"));
        }
        $user = $request->user();

        $max = $user->wallet($token->cryptoNetwork)->balance($token) - $user->saleAmount($token);
        $min = $token->cryptoNetwork->withdraw_fees + 1;
        $request->validate([
            'google_2fa_code' => ['required', 'digits:6'],
            'otp_password' => ['required'],
            'amount' => ['required', 'numeric', 'gte:' . $min, 'lte:' . $max],
            'wallet_address' => ['required']
        ]);
        // if (!CryptoWallet::validateExternal($request->wallet_address, $token)) {
        //     abort(ResponseStatus::BAD_REQUEST, __("messages.Wallet address is invalid"));
        // }
        if ($request->wallet_address == $user->wallet($token->cryptoNetwork)->base58_check) {
            abort(ResponseStatus::BAD_REQUEST, __("messages.Wallet address is invalid"));
        }
        if (env("APP_ENV") != "local") {
            if (!$user->google2fa_secret || !Google2FA::verify($request->google_2fa_code, $user->google2fa_secret)) abort(ResponseStatus::BAD_REQUEST, __('messages.2FA failed'));
            if (!$user->otp || !$user->otp->verify($request->otp_password, OtpAbility::getWithdrawOtpAbility())) abort(ResponseStatus::UNAUTHENTICATED, __('messages.OTP is invalid'));
        }
        if ($token->cryptoNetwork->name == 'tron' && !Tron::validateAddress($request->wallet_address)->result) abort(ResponseStatus::BAD_REQUEST, __('messages.Invalid address'));

        $withdraw = Withdraw::create(array_merge(
            $request->only('amount', 'wallet_address'),
            [
                'token_id' => $token->id,
                'crypto_wallet_id' => $user->wallet($token->cryptoNetwork)->id,
                'withdraw_fees' => $token->cryptoNetwork->withdraw_fees
            ]
        ));

        return response()->json($withdraw);
    }
}
