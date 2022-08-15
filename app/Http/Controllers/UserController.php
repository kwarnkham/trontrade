<?php

namespace App\Http\Controllers;

use App\Constants\ResponseStatus;
use App\Events\UserRegistered;
use App\Mail\IdentifierAdded;
use App\Models\Agent;
use App\Models\CryptoNetwork;
use App\Models\OtpAbility;
use App\Models\Setting;
use App\Models\User;
use Google2FA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function reset2FA(Request $request, User $user)
    {
        if (
            !$user->google2fa_secret_verified_at ||
            $user->roles->contains(function ($role) {
                return $role->id == 1;
            })
        ) return response()->json($user);
        $user->google2fa_secret_verified_at = null;
        $user->google2fa_secret = Google2FA::generateSecretKey(32, str_pad($user->id . time(), 20, 'X'));

        $user->timelessSave();
        return response()->json($user);
    }

    public function ban(Request $request, User $user)
    {
        if (
            $user->banned_at ||
            $user->roles->contains(function ($role) {
                return $role->id == 1;
            })
        ) return response()->json($user);
        $user->banned_at = now();
        $user->timelessSave();
        return response()->json($user);
    }

    public function unban(Request $request, User $user)
    {
        if (
            !$user->banned_at ||
            $user->roles->contains(function ($role) {
                return $role->id == 1;
            })
        ) return response()->json($user);
        $user->banned_at = null;
        $user->timelessSave();
        return response()->json($user);
    }

    public function index(Request $request)
    {
        return response()->json(User::with(['agent', 'cryptoWallets.tokens'])->whereNotIn(
            'users.id',
            DB::table('role_user')->where('role_id', 1)->pluck('user_id')
        )->filter($request->only(['email', 'email_verified', 'banned', 'wallet', 'agent']))->paginate((int)$request->per_page ?? 15));
    }

    public function setSetting(Request $request)
    {
        $data = $request->validate([
            'hidden_tokens' => ['required', 'array'],
            'hidden_tokens.*' => ['numeric', 'exists:tokens,id', "distinct"]
        ]);
        $user = $request->user();
        if ($user->setting != null) abort(ResponseStatus::BAD_REQUEST, __('messages.Cannot add another setting'));

        $hidden_tokens = rtrim(collect($data['hidden_tokens'])->reduce(function ($carry, $item) {
            return $carry .= "$item,";
        }, ""), ',');
        $data['hidden_tokens'] = $hidden_tokens;

        $user->setting()->create($data);
        return response()->json($user->refresh());
    }

    public function updateSetting(Request $request)
    {
        $data = $request->validate([
            'hidden_tokens' => ['required', 'array'],
            'hidden_tokens.*' => ['numeric', 'exists:tokens,id', "distinct"]
        ]);
        $user = $request->user();
        if ($user->setting == null) abort(ResponseStatus::BAD_REQUEST, __('messages.Setting does not exist'));

        $hidden_tokens = rtrim(collect($data['hidden_tokens'])->reduce(function ($carry, $item) {
            return $carry .= "$item,";
        }, ""), ',');
        $data['hidden_tokens'] = $hidden_tokens;

        $user->setting()->update($data);
        return response()->json($user->refresh());
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('email_verified_at', '!=', null);
                })
            ],
            'referrer_id' => ['required', env('APP_ENV') != 'production' ? '' : 'exists:users,id'],
            'password' => ['required', 'confirmed']
        ]);
        if (env('APP_ENV') != 'production') {
            $inviterId = User::getIdByInviterId($request->referrer_id);
            if (!$inviterId) abort(ResponseStatus::BAD_REQUEST, __("messages.Invalid Referrer ID"));
            $inviter = User::find($inviterId);
            if (!$inviter || !$inviter->email_verified_at) abort(ResponseStatus::BAD_REQUEST, __("messages.Invalid Referrer ID"));
            $data['referrer_id'] = $inviterId;
        }
        $data['password'] = bcrypt($data['password']);
        $agent = Agent::summon($request);
        if ($agent) $data['agent_id'] = $agent->id;
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            $user = User::create($data);
            $user->google2fa_secret = Google2FA::generateSecretKey(32, str_pad($user->id . time(), 20, 'X'));
            if (!$user->save()) {
                $user->delete();
                abort(ResponseStatus::SERVER_ERROR);
            }
        } else {
            $user->update($data);
        }

        try {
            $token = $user->createToken('');
            $user->ip = $request->ip();
            $user->save();
        } catch (\Throwable $th) {
            $user->tokens()->delete();
            abort(ResponseStatus::SERVER_ERROR, $th->getMessage());
        }
        Setting::setLocale($request, $user);
        event(new UserRegistered($user));

        return response()->json(
            [
                'token' => $token->plainTextToken,
                'user' => $user->load(['setting', 'identifiers'])
            ],
            ResponseStatus::CREATED
        );
    }

    public function verifyUser(Request $request)
    {
        $data = $request->validate([
            'identifier_id' => 'required|exists:identifiers,id',
            'first_name' => 'required|alpha',
            'middle_name' => '',
            'last_name' => 'required|alpha',
            'number' => ['required', 'min:7'],
            'images' => ['json']
        ]);
        unset($data['identifier_id']);
        $data['status'] = 1;
        $data['confirmed_at'] = null;
        $user = $request->user();
        $identifier = $user->identifiers()->where('identifier_id', 1)->first();

        if ($identifier && $identifier->identity->status != 3) abort(ResponseStatus::BAD_REQUEST);
        $user->first_name = $data['first_name'];
        $user->middle_name = $data['middle_name'];
        $user->last_name = $data['last_name'];
        $user->timelessSave();
        $user->identifiers()->syncWithPivotValues($request->identifier_id, $data);
        Mail::to(env('NOTICE_EMAIL'))->queue(new IdentifierAdded());
        return response()->json($user->load('identifiers'));
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
            'otp' => ['numeric']
        ]);
        $user = User::where('email', $data['email'])->firstOrFail();
        if ($user->banned_at) abort(ResponseStatus::UNAUTHORIZED, __('messages.You have been banned'));
        if (!Hash::check($data['password'], $user->password) || !$user->email_verified_at) {
            abort(ResponseStatus::UNAUTHENTICATED, __('messages.Wrong account or password'));
        }
        if ($request->has('otp')) {
            if (!$user->otp || !$user->otp->verify($data['otp'], OtpAbility::getLoginOtpAbility())) abort(ResponseStatus::UNAUTHENTICATED, __('messages.OTP is invalid'));
            $user->tokens()->delete();
            $token = $user->createToken('');
            $user->ip = $request->ip();
            $user->save();
            $user->sendSignedInNotification();
            return response()->json(['token' => $token->plainTextToken, 'user' => $user->load(['setting', 'identifiers', 'payments'])]);
        } else {
            Setting::setLocale($request, $user);
            if ($user->sendOTP(OtpAbility::getInstanceByName('login'))) {
                return response()->json(['user' => $user]);
            } else {
                abort(ResponseStatus::BAD_REQUEST, "Last OTP was created at " . $request->user()->otp->created_at);
            }
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(true);
    }

    public function verifyEmailOTP(Request $request)
    {
        $request->validate([
            'password' => ['required']
        ]);

        $user = $request->user();
        if ($user->otp != null && $user->otp->verify($request->password, OtpAbility::getVerfiyEmailOtpAbility())) {
            if (!$user->hasVerifiedEmail()) $user->markEmailAsVerified();
            return response()->json(['user' => $user]);
        } else {
            abort(ResponseStatus::BAD_REQUEST, __('messages.OTP is invalid'));
        }
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load(['setting', 'identifiers', 'payments']));
    }

    public function verifyEmailLink(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if ($user->otp != null && $user->otp->verify($request->password, OtpAbility::getVerfiyEmailOtpAbility())) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                $user->sendEmailVerified();
            }
            return redirect()->away(env("CLIENT_URL"));
        } else {
            return response()->json(__("messages.Link invaild or expired"));
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => ['required'],
            'new_password' => ['required', 'confirmed']
        ]);
        $user = $request->user();
        if (Hash::check($request->password, $user->password)) {
            $password = bcrypt($request->new_password);
            $user->password = $password;
            $user->timelessSave();
            $user->passwordChanges()->create([
                'type' => 'change password'
            ]);
            return response()->json(true);
        }
        abort(ResponseStatus::UNAUTHENTICATED, __("messages.Old password is incorrect"));
    }

    public function requestPasswordResetOTP(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email']
        ]);
        $user = User::getUserByEmail($request->email);
        Setting::setLocale($request, $user);
        if ($user->sendResetPassword(OtpAbility::getInstanceByName('reset_password'))) {
            return response()->json(true);
        } else {
            abort(ResponseStatus::BAD_REQUEST, "Last OTP was created at " . $user->otp->created_at);
        }
    }

    public function verifyResetPasswordOTP(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required']
        ]);
        $user = User::getUserByEmail($request->email);
        if ($user->otp->verify($request->password, OtpAbility::getInstanceByName('reset_password'))) {
            return response()->json(['signed_url' => URL::temporarySignedRoute(
                'resetPassword',
                now()->addMinutes(30),
                ['user_id' => $user->id]
            )]);
        } else {
            abort(ResponseStatus::BAD_REQUEST, __('messages.OTP is invalid'));
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed']
        ]);
        $user = User::findOrFail($request->user_id);
        $user->password = bcrypt($request->password);
        $user->tokens()->delete();
        $user->timelessSave();
        $user->passwordChanges()->create([
            'type' => 'reset password'
        ]);

        $user->sendPasswordResetNotification($request->ip());
        return response()->json(true);
    }

    public function getGoogle2FA(Request $request)
    {
        return Google2FA::getQRCodeInline(
            env("APP_NAME"),
            $request->user()->email,
            $request->user()->google2fa_secret
        );
    }

    public function verifyGoogle2FA(Request $request)
    {
        $request->validate([
            'google_2fa_code' => ['required', 'digits:6']
        ]);

        $user = $request->user();

        if ($user->google2fa_secret_verified_at != null) {
            abort(ResponseStatus::BAD_REQUEST, __('messages.The current QR code is already verified'));
        }
        if ($user->google2fa_secret != null && Google2FA::verify($request->google_2fa_code, $user->google2fa_secret)) {
            $user->google2fa_secret_verified_at = now();
            $user->timelessSave();
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }

    public function requestEmailVerificationLink(Request $request)
    {
        if ($request->user()->sendEmailVerificationLink()) {
            return response()->json(true);
        } else {
            abort(ResponseStatus::BAD_REQUEST, "Try again in 10 minutes");
        }
    }

    public function getWalletToken(Request $request, CryptoNetwork $cryptoNetwork)
    {
        $user = $request->user();
        $wallet = $user->wallet($cryptoNetwork);
        return response()->json($wallet->load('tokens'));
    }
}
