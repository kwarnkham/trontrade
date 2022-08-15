<?php

namespace App\Http\Controllers;

use App\Api\Tron;
use App\Constants\ResponseStatus;
use App\Models\CryptoNetwork;
use App\Models\Token;
use App\Models\User;
use App\Utility\TronWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CryptoNetworkController extends Controller
{
    public function index()
    {
        $networks = CryptoNetwork::with(['tokens'])->get();
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof User && $user->isAdmin()) {
            $networks->makeVisible(['balance', 'resources']);
        }
        return response()->json($networks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'unique:crypto_networks,name'],
            'icon' => ['url'],
            'api_url' => ['required', 'url'],
            'api_key' => ['required'],
            'trade_fees' => ['required', 'numeric', 'gte:1'],
            'withdraw_fees' => ['required', 'numeric', 'gte:1'],
            'address' => ['required', 'unique:crypto_wallets,base58_check'],
            'private_key' => ['required']
        ]);

        if (!Tron::validateAddress($request->address)->result) abort(ResponseStatus::BAD_REQUEST, __('messages.Invalid address'));
        $cryptoNetwork = CryptoNetwork::create($data);
        $cryptoNetwork->setBalance();
        return response()->json($cryptoNetwork);
    }

    public function update(Request $request, CryptoNetwork $network)
    {
        $data = $request->validate([
            'icon' => ['url'],
            'api_url' => ['required', 'url'],
            'api_key' => ['min:5'],
            'trade_fees' => ['required', 'numeric', 'gte:1'],
            'withdraw_fees' => ['required', 'numeric', 'gte:1'],
            'address' => ['required', 'unique:crypto_wallets,base58_check'],
            'private_key' => ['min:5']
        ]);

        if (!Tron::validateAddress($request->address)->result) abort(ResponseStatus::BAD_REQUEST, __('messages.Invalid address'));
        $network->update($data);
        $network->setBalance();
        return response()->json($network);
    }
}
