<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
{
    public function index()
    {
        return response()->json(Token::with(['cryptoNetwork'])->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'unique:tokens,name'],
            'display_name' => ['required'],
            'unit' => ['required'],
            'address' => ['required'],
            'decimals' => ['required'],
            'crypto_network_id' => ['exists:crypto_networks,id']
        ]);

        $token = Token::create($data);

        return response()->json($token);
    }

    public function summery(Request $request, Token $token)
    {
        return response()->json([
            'token' => $token->withoutRelations(),
            'amount' => User::whereNotIn(
                'users.id',
                DB::table('role_user')->where('role_id', 1)->pluck('user_id')
            )->filter($request->only(['email', 'email_verified', 'banned', 'wallet', 'agent']))->get()->reduce(function ($carry, $user) use ($token) {
                $wallet = $user->wallet($token->cryptoNetwork);
                if ($wallet)
                    return $carry + $wallet->tokens()->where('token_id', $token->id)->first()->pivot->balance;
                else return $carry;
            }, 0),
        ]);
    }
}
