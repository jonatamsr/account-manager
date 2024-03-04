<?php

namespace App\Repositories;

use App\Dtos\Events\DepositDto;
use App\Enums\AccountEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class AccountRepository
{
    private const DEFAULT_ACCOUNT = 100;

    public static function getBalance(int $id): float
    {
        // TODO: Exchange this with eloquent model ->firstOrFail() method after database is implemented
        throw_if(
            $id !== self::DEFAULT_ACCOUNT,
            new ModelNotFoundException('Model not found.', Response::HTTP_NOT_FOUND)
        );

        return 20;
    }

    public static function deposit(DepositDto $dto): float
    {
        $existingAccount = Cache::get(AccountEnum::ACCOUNT_CACHE_KEY . $dto->destination);
        if (is_null($existingAccount)) {
            self::create($dto);

            return $dto->amount;
        }

        $newBalance = $existingAccount['balance'] + $dto->amount;
        Cache::set(
            AccountEnum::ACCOUNT_CACHE_KEY . $dto->destination,
            ['balance' => $newBalance]
        );

        return $newBalance;
    }

    public static function create(DepositDto $dto): void
    {
        Cache::set(
            AccountEnum::ACCOUNT_CACHE_KEY . $dto->destination,
            [
                'id' => $dto->destination,
                'balance' => $dto->amount,
            ]
        );
    }
}
