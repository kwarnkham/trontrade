<?php

namespace App\Http\Controllers;

use App\Constants\ResponseStatus;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'enabled' => ['boolean']
        ]);
        return Payment::with(['currency'])->filter($request->only(['enabled', 'name']))->get();
    }

    public function removePayment(Request $request, Payment $payment)
    {
        $user = $request->user();
        $sales = $user->sales;
        foreach ($sales as $sale) {
            if ($user->payments()->where('currency_id', $sale->currency_id)->count() == 1) {
                abort(ResponseStatus::BAD_REQUEST, __("messages.Cannot remove the last payment method which currency is linked to existing token sale"));
            }
        }
        if (!$user->payments()->detach($payment->id)) abort(ResponseStatus::SERVER_ERROR, __("messages.Error removing payment"));

        return response()->json($user->payments);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'chinese_name' => ['required'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'icon' => ['required', 'url'],
            'country' => ['required'],
            'color' => ['required'],
            'enabled' => ['boolean'],
            'type' => [Rule::in([1, 2])]
        ]);

        $payment = Payment::create($data);

        return response()->json($payment);
    }

    public function usablePayments(Request $request)
    {
        return response()->json(Payment::with('currency')->whereIn('id', $request->user()->usablePayments())->get());
    }

    public function addPayment(Request $request, Payment $payment)
    {
        $user = $request->user();
        if ($user->payments->contains(function ($value) use ($payment) {
            return $value->id == $payment->id;
        })) {
            abort(ResponseStatus::BAD_REQUEST, __("messages.The selected payment is already added"));
        }
        $data = $request->validate([
            'account' => ['required', 'min:7'],
            'bank_name' => '',
            'bank_branch' => '',
            'bank_username' => [''],
            'mobile' => [''],
            'qr' => [''],
            'remark' => ['']
        ]);
        $user->payments()->attach($payment->id, $data);

        return response()->json($user->load(['payments']));
    }

    public function updatePayment(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'account' => ['required', 'min:7'],
            'bank_name' => '',
            'bank_branch' => '',
            'bank_username' => [''],
            'mobile' => [''],
            'enabled' => ['boolean'],
            'qr' => [''],
            'remark' => ['']
        ]);
        $user = $request->user();
        $user->payments()->updateExistingPivot($payment->id, $data);

        return response()->json($user->load(['payments']));
    }
}
