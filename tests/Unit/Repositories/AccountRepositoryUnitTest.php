<?php

namespace Tests\Unit\Repositories;

use App\Dtos\Events\DepositDto;
use App\Dtos\Events\TransferDto;
use App\Dtos\Events\WithdrawDto;
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

    public function testWithdrawMustDecreaseExistentAccountBalanceAndReturnNewBalance(): void
    {
        $accountId = 100;

        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . $accountId,
            [
                'id' => $accountId,
                'balance' => 20,
            ]
        );

        $withdrawDto = new WithdrawDto();
        $withdrawDto->attachValues([
            'origin' => $accountId,
            'amount' => 5,
        ]);

        $result = AccountRepository::withdraw($withdrawDto);

        $this->assertEquals(15, $result);
    }

    public function testWithdrawMustThrowModelNotFoundExceptionWhenInformedAccountDoesNotExist(): void
    {
        $accountId = -1;

        $withdrawDto = new WithdrawDto();
        $withdrawDto->attachValues([
            'origin' => $accountId,
            'amount' => 5,
        ]);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Model not found.');

        AccountRepository::withdraw($withdrawDto);
    }

    public function testTransferMustUpdateOriginAndDestinationBalancesAndReturnNewBalances(): void
    {
        $originAccountId = 200;
        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . $originAccountId,
            [
                'id' => $originAccountId,
                'balance' => 1000,
            ]
        );

        $destinationAccountId = 300;
        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . $destinationAccountId,
            [
                'id' => $destinationAccountId,
                'balance' => 2000,
            ]
        );

        $transferDto = new TransferDto();
        $transferDto->attachValues([
            'origin' => $originAccountId,
            'amount' => 500,
            'destination' => $destinationAccountId,
        ]);

        $result = AccountRepository::transfer($transferDto);

        $expectedResult = [
            'origin' => [
                'balance' => 500.0,
            ],
            'destination' => [
                'balance' => 2500.0,
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function testTransferMustThrowNotFoundUpdateWhenOriginAccountDoesNotExist(): void
    {
        $originAccountId = -1;

        $destinationAccountId = 300;
        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . $destinationAccountId,
            [
                'id' => $destinationAccountId,
                'balance' => 2000,
            ]
        );

        $transferDto = new TransferDto();
        $transferDto->attachValues([
            'origin' => $originAccountId,
            'amount' => 500,
            'destination' => $destinationAccountId,
        ]);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Model not found.');

        AccountRepository::transfer($transferDto);
    }

    public function testTransferMustThrowNotFoundUpdateWhenDestinationAccountDoesNotExist(): void
    {
        $originAccountId = 200;
        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . $originAccountId,
            [
                'id' => $originAccountId,
                'balance' => 1000,
            ]
        );

        $destinationAccountId = -1;

        $transferDto = new TransferDto();
        $transferDto->attachValues([
            'origin' => $originAccountId,
            'amount' => 500,
            'destination' => $destinationAccountId,
        ]);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Model not found.');

        AccountRepository::transfer($transferDto);
    }
}
