<?php

namespace App\Http\Controllers;

use App\Services\LineService;
use App\Services\NotionService;
use Illuminate\Http\JsonResponse;

class CronController extends Controller
{
    public function daily(NotionService $notion, LineService $line): JsonResponse
    {
        $data    = $notion->fetchDaily();
        $message = $line->formatDailyMessage($data);

        return response()->json([
            'status'  => 'ok',
            'data'    => $data,
            'message' => $message,
        ]);
    }
}
