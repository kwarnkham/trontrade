<?php

namespace App\Http\Controllers;

use App\Utility\Utility;
use Illuminate\Http\Request;

class XController extends Controller
{
    public function getGoogleFormFields(Request $request)
    {
        $request->validate([
            'name' => ['required', 'alpha_dash']
        ]);
        return Utility::generate_v4_post_policy($request->user()->id . $request->name);
    }

    public function getGoogleFormFieldsQr(Request $request)
    {
        $request->validate([
            'payment_id' => ['required', 'exists:payments,id']
        ]);
        return Utility::generate_v4_post_policy("u" . $request->user()->id . "p" . $request->payment_id . 'qr');
    }

    // public function tronEvent(Request $request)
    // {
    //     $request->validate([
    //         'event' => ['required']
    //     ]);

    //     Log::channel('tron_events')->info("Event received:: " . json_encode($request->all()));

    //     if ($request->event['name'] == 'Approval') {
    //         // $wallet = CryptoWallet::where('hex_address', $request->event['result']['owner'])->first();
    //         // if (!$wallet) return;
    //         // Log::channel('the_tron_events')->info("Event received:: " . json_encode($request->all()));
    //         // UserBalanceUpdated::dispatch($wallet);
    //     } else if ($request->event['name'] == 'Transfer') {
    //         $toWallet = CryptoWallet::where('hex_address', $request->event['result']['to'])->first();
    //         $fromWallet = CryptoWallet::where('hex_address', $request->event['result']['from'])->first();
    //         if (!$toWallet || ($toWallet && $fromWallet)) return;
    //         Log::channel('the_tron_events')->info("Event received:: " . json_encode($request->all()));
    //         $token = Token::where('name', 'tron_usdt')->first();
    //         $amount = $request->event['result']['value'] / pow(10, $token->decimals);
    //         $toWallet->increaseBalance($token, $amount);
    //     }
    // }
}
