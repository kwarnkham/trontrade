<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIdentityIsConfirmed
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
        if ($request->user() == null || !in_array(2, $request->user()->identifiers->map(function ($value) {
            return $value->identity->status;
        })->toArray())) {
            abort(403, __('messages.Identity needs confirmation'));
        }
        return $next($request);
    }
}
