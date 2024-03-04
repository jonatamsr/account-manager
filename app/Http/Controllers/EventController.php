<?php

namespace App\Http\Controllers;

use App\Dtos\Events\DepositDto;
use App\Dtos\Events\TransferDto;
use App\Dtos\Events\WithdrawDto;
use App\Services\EventService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;

class EventController extends Controller
{
    private const EVENT_DTO_BY_TYPE = [
        'deposit' => DepositDto::class,
        'withdraw' => WithdrawDto::class,
        'transfer' => TransferDto::class,
    ];

    public function dispatchEvent(Request $request): JsonResponse
    {
        $this->validate($request, [
            'type' => 'required|string'
        ]);

        $eventType = $request->input('type');

        $eventDtoClass = self::EVENT_DTO_BY_TYPE[$eventType];
        $eventDto = new $eventDtoClass();
        $eventDto->attachValues($request->toArray());

        /** @var EventService $eventService */
        $eventService = app(EventService::class);

        try {
            $response = $eventService->$eventType($eventDto);

            return response()->json($response, Response::HTTP_CREATED);
        } catch (ModelNotFoundException $exception) {
            return response()->json(0, Response::HTTP_NOT_FOUND);
        }
    }
}
