<?php

namespace App\Http\Middleware;

use App\Contracts\MustVerifyEmail;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $redirectToRoute = null)
    {
        $request->validate([
            'email' => [Rule::requiredIf($request->user_id == null && $request->user() == null), 'email', 'exists:users,email']
        ]);
        $user = $request->user();
        if (!$user)
            $user = User::where('email', $request->email)->first() ?? User::find($request->user_id);

        if (
            !$user ||
            ($user instanceof MustVerifyEmail &&
                !$user->hasVerifiedEmail())
        ) {
            return $request->expectsJson()
                ? abort(403, __('messages.Your email address is not verified'))
                : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
        }

        return $next($request);
    }
}
