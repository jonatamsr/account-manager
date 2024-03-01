<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;

class AccountController extends Controller
{
    public function getBalance(Request $request): JsonResponse
    {
        $this->validate($request, [
            'account_id' => 'required|integer'
        ]);

        /** @var AccountService $service */
        $service = app(AccountService::class);
        $accountId = $request->input('account_id');

        try {
            $response = $service->getBalance($accountId);

            return response()->json($response, Response::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json(0, Response::HTTP_NOT_FOUND);
        }
    }
}
