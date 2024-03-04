<?php

namespace App\Services;

use App\Dtos\Events\DepositDto;
use App\Dtos\Events\TransferDto;
use App\Dtos\Events\WithdrawDto;
use App\Repositories\AccountRepository;
use Illuminate\Support\Arr;

class EventService
{
    public function deposit(DepositDto $dto): array
    {
        return [
            'destination' => [
                'id' => $dto->destination,
                'balance' => AccountRepository::deposit($dto),
            ],
        ];
    }

    public function withdraw(WithdrawDto $dto): array
    {
        return [
            'origin' => [
                'id' => $dto->origin,
                'balance' => AccountRepository::withdraw($dto),
            ],
        ];
    }

    public function transfer(TransferDto $dto): array
    {
        $result = AccountRepository::transfer($dto);

        return [
            'origin' => [
                'id' => $dto->origin,
                'balance' => Arr::get($result, 'origin.balance'),
            ],
            'destination' => [
                'id' => $dto->destination,
                'balance' => Arr::get($result, 'destination.balance'),
            ],
        ];
    }
}
