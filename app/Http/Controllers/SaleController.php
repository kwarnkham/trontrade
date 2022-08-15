<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\Token;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'payment_id' => [Rule::in(Payment::enabledPayments()->pluck('id')->toArray())],
            'price_sort' => ['in:asc,desc']
        ]);
        $filters = $request->only(['currency_id', 'amount', 'price_sort']);
        if ($request->has('payment_id')) {
            $payment = Payment::findOrFail($request->payment_id);
            $user_ids = $payment->users()->wherePivot('enabled', 1)->pluck('users.id')->unique()->toArray();
            $filters['user_ids'] = $user_ids;
            $filters['payment_currency_id'] = $payment->currency_id;
        }
        // $user = Auth::guard('sanctum')->user();

        return response()->json(
            Sale::filter($filters)
                // ->when(
                //     $user ?? false,
                //     function ($query, $user) {
                //         return $query->where('user_id', '!=', $user->id);
                //     }
                // )
                ->whereRaw('(
                    SELECT SUM(total) as total FROM 
                    (SELECT SUM(tt_purchases.amount) as total FROM tt_purchases WHERE STATUS in (1, 2, 3, 5, 7, 8, 11, 13) AND tt_purchases.sale_id = tt_sales.id 
                    UNION ALL 
                    SELECT tt_crypto_networks.trade_fees FROM tt_crypto_networks, tt_tokens WHERE tt_crypto_networks.id = tt_tokens.crypto_network_id AND tt_tokens.id = tt_sales.token_id
                    UNION ALL 
                    SELECT min as total FROM tt_sales s WHERE s.id = tt_sales.id) as alias
                    ) <= tt_sales.amount')
                ->with(['token', 'currency'])
                ->paginate($request->has("per_page") ? (int)$request->per_page : 10)
        );
    }

    public function selfSale(Request $request)
    {
        return response()->json(Sale::where('user_id', $request->user()->id)->with(['token'])->paginate($request->has("per_page") ? (int)$request->per_page : 15));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $token = Token::findOrFail($request->token_id);
        $tradeFees = $token->cryptoNetwork->trade_fees;
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gte:0', "lte:" . $user->wallet($token->cryptoNetwork)->balance($token)],
            'min' => ['required', 'numeric', 'gte:' . (1 + $tradeFees), "lte:" . $request->amount],
            'max' => ['required', 'numeric', 'gte:' . $request->min, "lte:" . $request->amount],
            'token_id' => [
                'required', 'exists:tokens,id', Rule::unique('sales', 'token_id')->where(function ($query) use ($request) {
                    return $query->where('user_id', '=', $request->user()->id);
                })
            ],
            'currency_id' => ['required', Rule::in($request->user()->usableCurrencies()->toArray())],
            'price' => ['required', 'numeric', 'gt:0']
        ]);
        $sale = $user->sales()->create($data);
        return response()->json($sale->load('currency'));
    }

    public function update(Request $request, Sale $sale)
    {
        $token = $sale->token;
        $tradeFees = $token->cryptoNetwork->trade_fees;

        $data = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'gte:0',
                'gte:' . ($sale->minPool()),
                "lte:" . $sale->sellableAmount()
            ],
            'min' => ['required', 'numeric', 'gte:' . (1 + $tradeFees), "lte:" . $request->amount],
            'max' => ['required', 'numeric', 'gte:' . $request->min, "lte:" . $request->amount],
            'currency_id' => ['required',  Rule::in($request->user()->usableCurrencies()->toArray())],
            'price' => ['required', 'numeric', 'gt:0']
        ]);
        $sale->update($data);
        return response()->json($sale);
    }
}
