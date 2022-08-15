<?php

namespace App\Http\Controllers;

use App\Constants\ResponseStatus;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'sort' => ['in:desc,asc']
        ]);
        return response()->json(Purchase::with(['sale.token', 'user', 'sale.user', 'sale.currency', 'payment', 'tradeFees.networkTransaction', 'sale.token.cryptoNetwork'])->filter($request->only(['status', 'id', 'buyer_email', 'seller_email', 'sort', 'sale_id']))->paginate((int)$request->per_page ?? 10));
    }

    public function store(Request $request)
    {
        $sale = Sale::findOrFail($request->sale_id);
        $user = $request->user();
        $tradeFees = $sale->network()->trade_fees;
        $request->validate([
            'sale_id' => ['required', 'exists:sales,id', Rule::notIn(Sale::where('user_id', $user->id)->pluck('id')->unique()->toArray())],
            'amount' => ['required', 'numeric', 'gte:' . $sale->min, 'lte:' . $sale->buyableAmount(), 'lte:' . $sale->max, 'gte:' . (1 + $tradeFees)],
        ]);

        if ($user->purchases->contains(function ($purchase) {
            return in_array($purchase->status, [1, 2, 3, 5, 7, 8, 11]);
        })) {
            abort(ResponseStatus::BAD_REQUEST, __("messages.Order already existed"));
        }

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'sale_id' => $request->sale_id,
            'unit_price' => $sale->price,
            'trade_fees' => $sale->network()->trade_fees,
            'from' => $sale->user->wallet($sale->token->cryptoNetwork)->base58_check,
            'to' => $user->wallet($sale->token->cryptoNetwork)->base58_check,
            'token_id' => $sale->token_id,
        ]);

        return response()->json($purchase->load(['sale.currency', 'tradeFees.networkTransaction', 'sale.token.cryptoNetwork']));
    }

    public function getPurchases(Request $request)
    {
        return response()->json(
            Purchase::with([
                'payment.currency',
                'sale.user',
                'sale.token',
                'user',
                'payment',
                'sale.currency',
                'tradeFees.networkTransaction',
                'sale.token.cryptoNetwork'
            ])
                ->filter($request->only([
                    'status',
                    'id',
                    'buyer_email',
                    'seller_email',
                    'sort',
                    'sale_id'
                ]))
                ->where('user_id', $request->user()->id)->paginate($request->per_page ?? 10)
        );
    }

    public function getSalePurchases(Request $request, Sale $sale)
    {
        if ($request->user()->id != $sale->user->id) abort(ResponseStatus::UNAUTHENTICATED);
        return response()->json(
            Purchase::with([
                'payment.currency',
                'sale.user',
                'sale.token',
                'user',
                'payment',
                'sale.currency',
                'tradeFees.networkTransaction',
                'sale.token.cryptoNetwork'
            ])
                ->filter(array_merge($request->only([
                    'status',
                    'id',
                    'buyer_email',
                    'seller_email',
                    'sort',
                ]), ['sale_id' => $sale->id]))->paginate($request->per_page ?? 10)
        );
    }

    public function paid(Request $request, Purchase $purchase)
    {
        $request->validate([
            'payment_id' => [
                'required',
                Rule::in($purchase->sale->user->usablePayments()),
            ],
        ]);
        $payment = Payment::find($request->payment_id);
        if ($payment->currency->id != $purchase->sale->currency_id) abort(ResponseStatus::BAD_REQUEST, __("messages.The payment doesn't support the currency"));
        if ($request->user()->id != $purchase->user->id) abort(ResponseStatus::UNAUTHORIZED);
        // $request->user()->payments()->where('payment_id', $request->payment_id)->first()
        if ($purchase->status != 1) abort(ResponseStatus::BAD_REQUEST, __("messages.Can only pay a pending purchase"));
        if ($purchase->paid($payment->users()->where('user_id', $purchase->sale->user->id)->first()->details)) return response()->json($purchase->load(['tradeFees.networkTransaction', 'sale.token.cryptoNetwork']));
        else abort(ResponseStatus::SERVER_ERROR, __("messages.Error saving purchase"));
    }

    public function dealt(Request $request, Purchase $purchase)
    {
        if ($request->user()->id != $purchase->sale->user->id) abort(ResponseStatus::UNAUTHORIZED, 'unauthorized');
        if ($purchase->status != 2) abort(ResponseStatus::BAD_REQUEST, __("messages.Can only deal a paid purchase"));
        if ($purchase->dealt()) return response()->json($purchase->load(['tradeFees.networkTransaction', 'sale.token.cryptoNetwork']));
        else abort(ResponseStatus::SERVER_ERROR, __("messages.Error saving purchase"));
    }

    public function cancel(Request $request, Purchase $purchase)
    {
        if ($request->user()->id != $purchase->user->id) abort(ResponseStatus::UNAUTHORIZED);
        if ($purchase->status != 1) abort(ResponseStatus::BAD_REQUEST, "Can only cancle a pending purchase");
        $purchase->status = 4;
        $purchase->save();

        return response()->json($purchase->load(['tradeFees.networkTransaction', 'sale.token.cryptoNetwork']));
    }

    public function reject(Request $request, Purchase $purchase)
    {
        if ($request->user()->id != $purchase->sale->user->id) abort(ResponseStatus::UNAUTHORIZED);
        if (!in_array($purchase->status, [1, 2])) abort(ResponseStatus::BAD_REQUEST, __("messages.Can only reject a pending or paid purchase"));
        $purchase->status = 5;
        $purchase->save();

        return response()->json($purchase->load(['tradeFees.networkTransaction', 'sale.token.cryptoNetwork']));
    }

    public function getPurchase(Request $request, Purchase $purchase)
    {
        if ($request->user()->id != $purchase->user->id) abort(ResponseStatus::UNAUTHORIZED, __("messages.Not user's purchase"));
        $purchase->unsetRelation('user');
        return response()->json($purchase->load(['payment.currency', 'sale.user', 'tradeFees.networkTransaction', 'sale.token.cryptoNetwork']));
    }

    public function review(Request $request, Purchase $purchase)
    {
        if ($purchase->status != 5) abort(ResponseStatus::BAD_REQUEST, __("messages.Can only review a rejected purchase"));
        $purchase->status = 7;
        $purchase->save();

        return response()->json($purchase->load(['tradeFees.networkTransaction', 'sale.token.cryptoNetwork']));
    }

    public function reviewed(Request $request, Purchase $purchase)
    {
        $request->validate([
            'status' => ['required', 'in:8,9']
        ]);
        if ($purchase->status != 7) abort(ResponseStatus::BAD_REQUEST, __("messages.Wrong purchase to review"));
        if ($purchase->reviewed($request->status)) return response()->json($purchase->load(['tradeFees.networkTransaction', 'sale.token.cryptoNetwork']));
        else abort(ResponseStatus::SERVER_ERROR, __("messages.Error saving purchase"));
    }
}
