<?php

namespace Tests\Integration\Controllers;

use App\Enums\AccountEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AccountControllerIntegrationTest extends TestCase
{
    /** @dataProvider dataProviderForGetBalance */
    public function testGetBalance($accountId, $expectedStatusCode, $expectedJson): void
    {
        Cache::add(
            AccountEnum::ACCOUNT_CACHE_KEY . 100,
            [
                'id' => 100,
                'balance' => 20,
            ]
        );

        $this->get("balance?account_id=$accountId")
            ->seeStatusCode($expectedStatusCode)
            ->seeJson($expectedJson);
    }

    public static function dataProviderForGetBalance(): array
    {
        return [
            'mustReturnBalanceCorrectlyWhenAccountExists' => [
                'accountId' => 100,
                'expectedStatusCode' => Response::HTTP_OK,
                'expectedJson' => [20],
            ],
            'mustTreatExceptionAndReturnNotFoundWhenAccountDoesNotExist' => [
                'accountId' => -1,
                'expectedStatusCode' => Response::HTTP_NOT_FOUND,
                'expectedJson' => [0],
            ],
        ];
    }
}
