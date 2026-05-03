<?php

namespace App\Http\Controllers;

use App\Services\LineService;
use App\Services\NotionService;
use Illuminate\Http\JsonResponse;

class CronController extends Controller
{
    public function daily(NotionService $notion, LineService $line): JsonResponse
    {
        try {
            $data    = $notion->fetchDaily();
            $message = $line->formatDailyMessage($data);
            $userId  = config('services.line.user_id');

            $line->pushWithNotionLink($userId, $message);

            return response()->json([
                'status'  => 'ok',
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
                'file'   => $e->getFile(),
                'line'   => $e->getLine(),
            ], 500);
        }
    }

    public function evening(NotionService $notion, LineService $line): JsonResponse
    {
        try {
            $data    = $notion->fetchDaily();
            $message = $line->formatEveningMessage($data);
            $userId  = config('services.line.user_id');

            $line->pushWithNotionLink($userId, $message);

            return response()->json([
                'status'  => 'ok',
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage(),
                'file'   => $e->getFile(),
                'line'   => $e->getLine(),
            ], 500);
        }
    }
}
