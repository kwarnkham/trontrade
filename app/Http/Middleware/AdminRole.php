<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminRole
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
        if ($request->user() == null || $request->user()->roles()->where('name', 'admin')->first() == null) {
            abort(403, __('messages.Not permitted action'));
        }
        return $next($request);
    }
}
