<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Ensure2FAEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() == null || $request->user()->google2fa_secret_verified_at == null) {
            abort(403, __('messages.Two factor authentication needed'));
        }
        return $next($request);
    }
}
