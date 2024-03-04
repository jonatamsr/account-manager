<?php

namespace Tests\Unit\Services;

use App\Dtos\Events\DepositDto;
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

        Cache::set(
            AccountEnum::ACCOUNT_CACHE_KEY . 1,
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
}
