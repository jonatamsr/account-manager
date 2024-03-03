<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Routing\Controller;

class MaintenanceController extends Controller
{
    public function reset(): JsonResponse
    {
        Cache::clear();

        return response()->json('OK', Response::HTTP_OK);
    }
}
