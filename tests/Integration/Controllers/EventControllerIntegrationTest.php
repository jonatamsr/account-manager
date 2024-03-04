<?php

namespace Tests\Integration\Controllers;

use App\Enums\AccountEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EventControllerIntegrationTest extends TestCase
{
    public function testDepositMustCreateAccountWithInitialBalance(): void
    {
        $destinationAccountId = 1;

        $payload = [
            'type' => 'deposit',
            'destination' => $destinationAccountId,
            'amount' => 100,
        ];

        $expectedResponse = [
            'destination' => [
                'id' => $destinationAccountId,
                'balance' => 100,
            ],
        ];

        $this->post('event', $payload)
            ->seeStatusCode(Response::HTTP_CREATED)
            ->seeJson($expectedResponse);
    }

    public function testDepositMustIncreaseExistingAccountBalance(): void
    {
        $destinationAccountId = 1;

        Cache::set(
            AccountEnum::ACCOUNT_CACHE_KEY . 1,
            [
                'id' => $destinationAccountId,
                'balance' => 50,
            ]
        );

        $payload = [
            'type' => 'deposit',
            'destination' => $destinationAccountId,
            'amount' => 100,
        ];

        $expectedResponse = [
            'destination' => [
                'id' => $destinationAccountId,
                'balance' => 150,
            ],
        ];

        $this->post('event', $payload)
            ->seeStatusCode(Response::HTTP_CREATED)
            ->seeJson($expectedResponse);
    }

    public function testWithdrawMustDecreaseAccountBalanceAndReturn(): void
    {
        $originAccountId = 100;

        $payload = [
            'type' => 'withdraw',
            'origin' => $originAccountId,
            'amount' => 5,
        ];

        $expectedResponse = [
            'origin' => [
                'id' => $originAccountId,
                'balance' => 15,
            ],
        ];

        $this->post('event', $payload)
            ->seeStatusCode(Response::HTTP_CREATED)
            ->seeJson($expectedResponse);
    }

    public function testWithdrawMustThrowExceptionWhenInformedAccountDoesNotExist(): void
    {
        $originAccountId = -1;

        $payload = [
            'type' => 'withdraw',
            'origin' => $originAccountId,
            'amount' => 5,
        ];

        $this->post('event', $payload)
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJson([0]);
    }

    public function testDispatchEventMustThrowValidationErrorWhenTypeNotInformed(): void
    {
        $originAccountId = 100;

        $payload = [
            'type' => '',
            'origin' => $originAccountId,
            'amount' => 5,
        ];

        $this->post('event', $payload)
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->seeJson(["type" => ["The type field is required."]]);
    }
}
