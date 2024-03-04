<?php

namespace App\Services;

use App\Dtos\Events\DepositDto;
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
}
