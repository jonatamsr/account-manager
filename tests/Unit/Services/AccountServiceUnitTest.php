<?php

namespace Tests\Unit\Services;

use App\Enums\AccountEnum;
use App\Services\AccountService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AccountServiceUnitTest extends TestCase
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

        /** @var AccountService $service */
        $service = app(AccountService::class);

        $result = $service->getBalance($accountId);

        $this->assertEquals(20, $result);
    }

    public function testGetBalanceMustThrowModelNotFoundExceptionWhenAccountIdDoesNotExist(): void
    {
        $accountId = -1;

        /** @var AccountService $service */
        $service = app(AccountService::class);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Model not found.');

        $service->getBalance($accountId);
    }
}
