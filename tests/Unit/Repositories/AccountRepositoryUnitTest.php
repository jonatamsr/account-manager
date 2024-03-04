<?php

namespace Tests\Unit\Repositories;

use App\Dtos\Events\DepositDto;
use App\Enums\AccountEnum;
use App\Repositories\AccountRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AccountRepositoryUnitTest extends TestCase
{
    public function testGetBalanceMustReturnTwelveWhenAccountIdIsOneHundred(): void
    {
        $accountId = 100;

        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . $accountId,
            [
                'id' => $accountId,
                'balance' => 20,
            ]
        );

        $result = AccountRepository::getBalance($accountId);

        $this->assertEquals(20, $result);
    }

    public function testGetBalanceMustThrowModelNotFoundExceptionWhenAccountIdDoesNotExist(): void
    {
        $accountId = -1;

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Model not found.');

        AccountRepository::getBalance($accountId);
    }

    public function testDepositMustCreateAccountWithInitialBalanceWhenItDoesNotExist(): void
    {
        $destinationAccountId = 1;

        $depositDto = new DepositDto();
        $depositDto->attachValues([
            'destination' => $destinationAccountId,
            'amount' => 100,
        ]);

        $result = AccountRepository::deposit($depositDto);

        $this->assertEquals(100, $result);
    }

    public function testDepositMustIncreaseAlreadyExistentAccountBalance(): void
    {
        $destinationAccountId = 1;

        Cache::set(
            AccountEnum::ACCOUNT_CACHE_KEY . 1,
            [
                'id' => $destinationAccountId,
                'balance' => 50,
            ]
        );

        $depositDto = new DepositDto();
        $depositDto->attachValues([
            'destination' => $destinationAccountId,
            'amount' => 100,
        ]);

        $result = AccountRepository::deposit($depositDto);

        $this->assertEquals(150, $result);
    }
}
