<?php

namespace Tests\Unit\Controllers;

use App\Dtos\Events\DepositDto;
use App\Services\EventService;
use Illuminate\Http\Response;
use Tests\TestCase;

class EventControllerUnitTest extends TestCase
{
    public function testDepositMustCallServiceAndReturnResponseWithCreatedStatus(): void
    {
        $destinationAccountId = 1;

        $payload = [
            'type' => 'deposit',
            'destination' => $destinationAccountId,
            'amount' => 100,
        ];

        $serviceMock = $this->createMock(EventService::class);
        $this->app->instance(EventService::class, $serviceMock);
        $serviceMock->expects(self::once())
            ->method('deposit')
            ->with(self::callback(
                fn (DepositDto $dto) =>
                    $dto->amount == 100 && $dto->destination == $destinationAccountId)
            )
            ->willReturn(['fake-response']);

        $this->post('event', $payload)
            ->seeStatusCode(Response::HTTP_CREATED)
            ->seeJson(['fake-response']);
    }
}
