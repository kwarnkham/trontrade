<?php

namespace App\Http\Middleware;

use App\Constants\ResponseStatus;
use App\Models\CryptoNetwork;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EnsureSystemHasEnoughResource
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
        $minAmount = 200;
        $tronNetwork = Cache::rememberForever('tronNetwork', function () {
            return CryptoNetwork::where('name', 'tron')->first();
        });
        if ($tronNetwork->balance < $minAmount) {
            if ($tronNetwork->setBalance()) {
                $tronNetwork = $tronNetwork->refresh();
            }
            if ($tronNetwork->balance < $minAmount)
                abort(ResponseStatus::SERVICE_UNAVAILABLE);
        }
        return $next($request);
    }
}
