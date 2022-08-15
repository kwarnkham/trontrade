<?php

namespace App\Http\Controllers;

use App\Models\NetworkTransaction;
use Illuminate\Http\Request;

class NetworkTransactionController extends Controller
{
    public function index(Request $request)
    {
        // $request->validate([
        //     'token_id' => [Rule::exists('tokens', 'id')->where(function ($query) use ($cryptoNetwork) {
        //         return $query->where('crypto_network_id', $cryptoNetwork->id);
        //     })],
        // ]);
        $query = NetworkTransaction::with(['token'])->filter($request->only(['transaction_id', 'from', 'to', 'status', 'type', 'agent_id']));
        $user = $request->user();
        if (!$user->roles->contains(function ($role) {
            return $role->name == 'admin';
        })) {
            $query->where(function ($q) use ($user) {
                $q->orWhereIn('to', $user->cryptoWallets->map(function ($wallet) {
                    return $wallet->base58_check;
                })->toArray())->orWhereIn('from', $user->cryptoWallets->map(function ($wallet) {
                    return $wallet->base58_check;
                })->toArray());
            })->where('type', '!=', 'TradeFee');
        }
        return response()->json($query->orderBy('block_timestamp', 'asc')->paginate((int)$request->per_page ?? 10));
    }
}
