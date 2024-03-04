<?php

namespace Tests\Unit\Services;

use App\Dtos\Events\DepositDto;
use App\Dtos\Events\WithdrawDto;
use App\Enums\AccountEnum;
use App\Services\EventService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
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

        Cache::set(
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

    public function testWithdrawMustThrowModelNotFoundExceptionWhenAccountIdDoesNotExist(): void
    {
        $originAccountId = -1;

        $withdrawDto = new WithdrawDto();
        $withdrawDto->attachValues([
            'origin' => $originAccountId,
            'amount' => 5,
        ]);

        /** @var EventService $service */
        $service = app(EventService::class);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Model not found.');

        $service->withdraw($withdrawDto);
    }
}
