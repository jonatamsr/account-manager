<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;

class AccountController extends Controller
{
    public function getBalance(): JsonResponse
    {
        return response()->json(20, Response::HTTP_OK);
    }
}
