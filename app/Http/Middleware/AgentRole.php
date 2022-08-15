<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AgentRole
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
        if ($request->user() == null || !$request->user()->roles()->pluck('name')->contains(function ($role) {
            return in_array($role, ['admin', 'agent']);
        })) {
            abort(403, __('messages.Not permitted action'));
        }
        return $next($request);
    }
}
