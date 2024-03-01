<?php

namespace App\Services;

use App\Repositories\AccountRepository;

class AccountService
{
    public function getBalance(int $accountId): float
    {
        return AccountRepository::getBalance($accountId);
    }
}
