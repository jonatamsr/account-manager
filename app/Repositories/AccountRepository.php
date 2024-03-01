<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AccountRepository
{
    private const DEFAULT_ACCOUNT = 100;

    public static function getBalance(int $id): float
    {
        // TODO: Exchange this with eloquent model ->firstOrFail() method after database is implemented
        throw_if(
            $id !== self::DEFAULT_ACCOUNT,
            new ModelNotFoundException('Model not found', Response::HTTP_NOT_FOUND)
        );

        return 20;
    }
}
