<?php

namespace App\Http\Controllers;

use App\Services\NotionService;
use Illuminate\Http\JsonResponse;

class CronController extends Controller
{
    public function daily(NotionService $notion): JsonResponse
    {
        $data = $notion->fetchDaily();

        return response()->json([
            'status' => 'ok',
            'data'   => $data,
        ]);
    }
}
