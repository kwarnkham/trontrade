<?php

namespace App\Http\Middleware;

use App\Constants\ResponseStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsNotBanned
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
        $user = Auth::guard('sanctum')->user();
        if ($user && $user->banned_at) abort(ResponseStatus::UNAUTHORIZED, __('messages.You have been banned'));
        return $next($request);
    }
}
