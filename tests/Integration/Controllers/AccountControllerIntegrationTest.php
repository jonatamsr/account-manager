<?php

namespace Tests\Integration\Controllers;

use Illuminate\Http\Response;
use Tests\TestCase;

class AccountControllerIntegrationTest extends TestCase
{
    /** @dataProvider dataProviderForGetBalance */
    public function testGetBalance($accountId, $expectedStatusCode, $expectedJson): void
    {
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
