<?php

namespace App\Http\Middleware;

use App\Enums\AccountEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SetupInitialAccountMiddleware
{
    private const DEFAULT_ACCOUNT_ID = 300;
    private const DEFAULT_DESTINATION_ACCOUNT_INITIAL_BALANCE = 0;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . self::DEFAULT_ACCOUNT_ID,
            [
                'id' => self::DEFAULT_ACCOUNT_ID,
                'balance' => self::DEFAULT_DESTINATION_ACCOUNT_INITIAL_BALANCE,
            ]
        );

        return $next($request);
    }
}
