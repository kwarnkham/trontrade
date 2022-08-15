<?php

namespace App\Http\Middleware;

use App\Constants\ResponseStatus;
use App\Models\Agent;
use Closure;
use Illuminate\Http\Request;

class VerifyAgent
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
        if (!Agent::verify($request)) abort(ResponseStatus::UNAUTHORIZED);
        return $next($request);
    }
}
