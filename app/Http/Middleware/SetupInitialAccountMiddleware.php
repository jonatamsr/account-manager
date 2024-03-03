<?php

namespace App\Http\Middleware;

use App\Enums\AccountEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SetupInitialAccountMiddleware
{
    private const DEFAULT_ACCOUNT_ID = 100;
    private const DEFAULT_ACCOUNT_INITIAL_BALANCE = 20;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Cache::set(
            AccountEnum::ACCOUNT_CACHE_KEY . self::DEFAULT_ACCOUNT_ID,
            self::DEFAULT_ACCOUNT_INITIAL_BALANCE
        );

        return $next($request);
    }
}
