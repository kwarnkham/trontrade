<?php

namespace App\Http\Controllers;

use App\Constants\ResponseStatus;
use App\Models\OtpAbility;
use Illuminate\Http\Request;

class OneTimePasswordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'exists:otp_abilities,name'
        ]);
        if ($request->user()->sendOTP(OtpAbility::getInstanceByName($request->name))) {
            return response()->json(true);
        } else {
            abort(ResponseStatus::BAD_REQUEST, "Last OTP was created at " . $request->user()->otp->created_at);
        }
    }
}
