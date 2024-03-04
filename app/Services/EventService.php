<?php

namespace App\Services;

use App\Dtos\Events\DepositDto;
use App\Dtos\Events\WithdrawDto;
use App\Repositories\AccountRepository;

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
}
