<?php

namespace App\Http\Controllers;

use App\Models\OtpAbility;
use Illuminate\Http\Request;

class OtpAbilityController extends Controller
{
    public function index()
    {
        return response()->json(OtpAbility::all());
    }
}
