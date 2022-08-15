<?php

namespace App\Http\Controllers;

use App\Api\Tron;
use App\Constants\ResponseStatus;
use App\Models\Agent;
use App\Models\CryptoNetwork;
use App\Models\CryptoWallet;
use App\Models\NetworkTransaction;
use App\Models\Setting;
use App\Models\Token;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class AgentApiController extends Controller
{
    public function getUSDRate()
    {
        $redisKey = 'usd_rate';
        if (Redis::exists($redisKey)) {
            $usdRate = json_decode(Redis::get($redisKey));
        } else {
            $usdRate = collect(Http::get('https://www.pexpay.com/bapi/asset/v1/public/asset-service/product/currency')->object()->data)->first(function ($item) {
                return $item->pair == "CNY_USD";
            });
            Redis::set($redisKey, json_encode($usdRate), 'EX', 300);
        }
        return response()->json($usdRate);
    }

    public function createUser(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'unique:users,username'],
            'password' => ['required', 'confirmed']
        ]);
        $data['password'] = bcrypt($data['password']);
        $agent = Agent::summon($request);
        if ($agent) $data['agent_id'] = $agent->id;
        $user = User::create($data);
        try {
            $token = $user->createToken('');
            $user->ip = $request->ip();
            $user->save();
        } catch (\Throwable $th) {
            $user->tokens()->delete();
            abort(ResponseStatus::SERVER_ERROR, $th->getMessage());
        }
        Setting::setLocale($request, $user);

        foreach (CryptoNetwork::all() as $cryptoNetwork) {
            $wallet = CryptoWallet::create(array_merge(
                call_user_func("\\App\\Api\\" . ucwords($cryptoNetwork->name) . '::generateAddressLocally'),
                ['crypto_network_id' => $cryptoNetwork->id, 'user_id' => $user->id],
            ));
            $wallet->linkTokens();
        }

        return response()->json(
            [
                'token' => $token->plainTextToken,
                'user' => $user->load(['cryptoWallets.tokens'])
            ],
            ResponseStatus::CREATED
        );
    }

    public function getUserWallets(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id']
        ]);
        $agent = Agent::summon($request);
        $user = User::with(['cryptoWallets.cryptoNetwork', 'cryptoWallets.tokens'])->find($request->user_id);
        if ($user->agent_id != $agent->id) abort(ResponseStatus::UNAUTHORIZED);
        foreach ($user->cryptoWallets as $wallet) {
            foreach (Token::whereBelongsTo($wallet->cryptoNetwork)->get() as $token) {
                $wallet->syncWallet($token, true);
            }
        }
        $user->refresh();
        return response()->json($user->cryptoWallets->load(['tokens']));
    }

    public function getNetworkTransaction(Request $request)
    {
        $query = NetworkTransaction::with(['token'])->filter(array_merge($request->only(['transaction_id', 'from', 'to', 'status', 'type', 'agent_id', 'start_time', 'end_time']), ['agent_id' => Agent::summon($request)->id]));

        return response()->json($query->paginate((int)$request->per_page ?? 10));
    }

    public function getTokenSummery(Request $request, Token $token)
    {
        $agent = Agent::summon($request);
        $agent->load(['users.cryptoWallets']);
        return response()->json([
            'token' => $token->withoutRelations(),
            'amount' => $agent->users->reduce(function ($carry, $user) use ($token) {
                $wallet = $user->wallet($token->cryptoNetwork);
                if ($wallet)
                    return $carry + $wallet->tokens()->where('token_id', $token->id)->first()->pivot->balance;
                else return $carry;
            }, 0)
        ]);
    }

    public function withdraw(Request $request, Token $token, User $user)
    {
        if ($user->agent->id != Agent::summon($request)->id) abort(ResponseStatus::UNAUTHORIZED);
        $max = $user->wallet($token->cryptoNetwork)->balance($token) - $user->saleAmount($token);
        $fees = $token->cryptoNetwork->withdraw_fees * 2;
        $min = $fees + 1;
        $request->validate([
            'amount' => ['required', 'numeric', 'gte:' . $min, 'lte:' . $max],
            'wallet_address' => ['required']
        ]);
        if ($request->wallet_address == $user->wallet($token->cryptoNetwork)->base58_check) {
            abort(ResponseStatus::BAD_REQUEST, __("messages.Wallet address is invalid"));
        }
        if ($token->cryptoNetwork->name == 'tron' && !Tron::validateAddress($request->wallet_address)->result) abort(ResponseStatus::BAD_REQUEST, __('messages.Invalid address'));

        $withdraw = Withdraw::create(array_merge(
            $request->only('amount', 'wallet_address'),
            [
                'token_id' => $token->id,
                'crypto_wallet_id' => $user->wallet($token->cryptoNetwork)->id,
                'withdraw_fees' => $fees
            ]
        ));

        return response()->json($withdraw);
    }
}
