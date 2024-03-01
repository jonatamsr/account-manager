<?php

namespace Tests\Unit\Controllers;

use Illuminate\Http\Response;
use Tests\TestCase;

class AccountControllerUnitTest extends TestCase
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
            'mustThrowValidationExceptionWhenAccountIdNotInteger' => [
                'accountId' => 'foo',
                'expectedStatusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'expectedJson' => [
                    'account_id' => ["The account id must be an integer."]
                ],
            ],
        ];
    }
}
