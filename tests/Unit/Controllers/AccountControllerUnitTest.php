<?php

namespace Tests\Unit\Controllers;

use App\Services\AccountService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Tests\TestCase;

class AccountControllerUnitTest extends TestCase
{
    public function testGetBalanceMustCallServiceWithAccountId(): void
    {
        $accountId = 100;

        $accountServiceMock = $this->createMock(AccountService::class);
        $this->app->instance(AccountService::class, $accountServiceMock);
        $accountServiceMock->expects(self::once())
            ->method('getBalance')
            ->with($accountId)
            ->willReturn(50.0);

        $this->get("balance?account_id=$accountId")
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson([50]);
    }

    public function testGetBalanceMustReturnNotFoundWhenModelNotFoundExceptionIsThrown(): void
    {
        $accountId = -1;

        $accountServiceMock = $this->createMock(AccountService::class);
        $this->app->instance(AccountService::class, $accountServiceMock);
        $accountServiceMock->expects(self::once())
            ->method('getBalance')
            ->with($accountId)
            ->willThrowException(new ModelNotFoundException());

        $this->get("balance?account_id=$accountId")
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJson([0]);
    }

    public function testGetBalanceMustThrowValidationExceptionWhenAccountIdIsNull(): void
    {
        $accountServiceMock = $this->createMock(AccountService::class);
        $this->app->instance(AccountService::class, $accountServiceMock);
        $accountServiceMock->expects(self::never())
            ->method('getBalance');

        $this->get('balance?account_id=')
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->seeJson(['account_id' => ['The account id field is required.']]);
    }

    public function testGetBalanceMustThrowValidationExceptionWhenAccountIdIsNotInteger(): void
    {
        $accountServiceMock = $this->createMock(AccountService::class);
        $this->app->instance(AccountService::class, $accountServiceMock);
        $accountServiceMock->expects(self::never())
            ->method('getBalance');

        $this->get('balance?account_id=test')
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->seeJson(['account_id' => ['The account id must be an integer.']]);
    }
}
