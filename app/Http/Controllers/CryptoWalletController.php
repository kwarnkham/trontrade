<?php

namespace App\Http\Controllers;

use App\Constants\ResponseStatus;
use App\Models\CryptoWallet;
use App\Models\Token;
use Illuminate\Http\Request;

class CryptoWalletController extends Controller
{
    public function syncWallet(Request $request)
    {
        $request->validate([
            'token_id' => ['exists:tokens,id', 'required']
        ]);

        $token = Token::find($request->token_id);
        $cyptoWallet = $request->user()->cryptoWallets()->where('crypto_network_id', $token->crypto_network_id)->first();
        if (!$cyptoWallet) abort(ResponseStatus::BAD_REQUEST);
        $cyptoWallet->linkTokens();

        $cyptoWallet->syncWallet($token, true);

        return response()->json($cyptoWallet->load('tokens'));
    }

    public function getWallets(Request $request)
    {
        return response()->json(CryptoWallet::with('tokens', 'cryptoNetwork')->where('user_id', $request->user()->id)->paginate((int)$request->per_page ?? 20));
    }
}
