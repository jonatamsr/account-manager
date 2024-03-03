<?php

namespace Tests\Unit\Repositories;

use App\Repositories\AccountRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Tests\TestCase;

class AccountRepositoryUnitTest extends TestCase
{
// TODO: This unit test can be improved after database is implemented
    public function testGetBalanceMustReturnTwelveWhenAccountIdIsOneHundred(): void
    {
        $accountId = 100;

        /** @var AccountRepository $service */
        $repository = app(AccountRepository::class);

        $result = $repository->getBalance($accountId);

        $this->assertEquals(20, $result);
    }

    public function testGetBalanceMustThrowModelNotFoundExceptionWhenAccountIdDoesNotExist(): void
    {
        $accountId = -1;

        /** @var AccountRepository $service */
        $repository = app(AccountRepository::class);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Model not found.');

        $repository->getBalance($accountId);
    }
}
