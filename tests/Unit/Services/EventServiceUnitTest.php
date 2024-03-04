<?php

namespace Tests\Unit\Services;

use App\Dtos\Events\DepositDto;
use App\Dtos\Events\TransferDto;
use App\Dtos\Events\WithdrawDto;
use App\Enums\AccountEnum;
use App\Services\EventService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EventServiceUnitTest extends TestCase
{
    public function testDepositMustReturnFormattedResponseWhenAccountDoesNotExist(): void
    {
        $destinationAccountId = 1;

        /** @var EventService $service */
        $service = app(EventService::class);

        $depositDto = new DepositDto();
        $depositDto->attachValues([
            'destination' => $destinationAccountId,
            'amount' => 100,
        ]);

        $result = $service->deposit($depositDto);

        $expectedResult = [
            'destination' => [
                'id' => $destinationAccountId,
                'balance' => 100,
            ]
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function testDepositMustReturnFormattedResponseWhenAccountAlreadyExist(): void
    {
        $destinationAccountId = 1;

        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . $destinationAccountId,
            [
                'id' => $destinationAccountId,
                'balance' => 50,
            ]
        );

        /** @var EventService $service */
        $service = app(EventService::class);

        $depositDto = new DepositDto();
        $depositDto->attachValues([
            'destination' => $destinationAccountId,
            'amount' => 100,
        ]);

        $result = $service->deposit($depositDto);

        $expectedResult = [
            'destination' => [
                'id' => $destinationAccountId,
                'balance' => 150,
            ]
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function testWithdrawMustReturnFormattedResponse(): void
    {
        $originAccountId = 1;

        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . $originAccountId,
            [
                'id' => $originAccountId,
                'balance' => 50,
            ]
        );

        /** @var EventService $service */
        $service = app(EventService::class);

        $withdrawDto = new WithdrawDto();
        $withdrawDto->attachValues([
            'origin' => $originAccountId,
            'amount' => 5,
        ]);

        $result = $service->withdraw($withdrawDto);

        $expectedResult = [
            'origin' => [
                'id' => $originAccountId,
                'balance' => 45,
            ]
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function testTransferMustReturnFormattedResponse(): void
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

        /** @var EventService $service */
        $service = app(EventService::class);

        $transferDto = new TransferDto();
        $transferDto->attachValues([
            'origin' => $originAccountId,
            'amount' => 500,
            'destination' => $destinationAccountId,
        ]);

        $result = $service->transfer($transferDto);

        $expectedResult = [
            'origin' => [
                'id' => $originAccountId,
                'balance' => 500,
            ],
            'destination' => [
                'id' => $destinationAccountId,
                'balance' => 2500,
            ],
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
