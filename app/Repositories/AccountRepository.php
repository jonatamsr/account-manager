<?php

namespace App\Repositories;

use App\Dtos\Events\DepositDto;
use App\Dtos\Events\WithdrawDto;
use App\Enums\AccountEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class AccountRepository
{
    public static function getBalance(int $id): float
    {
        $account = Cache::get(AccountEnum::ACCOUNT_CACHE_KEY . $id);
        throw_if(
            is_null($account),
            new ModelNotFoundException('Model not found.', Response::HTTP_NOT_FOUND)
        );

        return $account['balance'];
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

    public static function withdraw(WithdrawDto $dto): float
    {
        $account = Cache::get(AccountEnum::ACCOUNT_CACHE_KEY . $dto->origin);
        throw_if(
            is_null($account),
            new ModelNotFoundException('Model not found.', Response::HTTP_NOT_FOUND)
        );

        $newBalance = $account['balance'] - $dto->amount;
        Cache::set(
            AccountEnum::ACCOUNT_CACHE_KEY . $dto->origin,
            ['balance' => $newBalance]
        );

        return $newBalance;
    }
}
