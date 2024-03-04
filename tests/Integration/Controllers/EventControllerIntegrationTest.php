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
}
