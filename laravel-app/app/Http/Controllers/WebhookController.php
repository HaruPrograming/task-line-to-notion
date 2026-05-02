<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * LINE からの Webhook リクエストを受け取る
     *
     * POST /api/webhook
     */
    public function handle(Request $request): JsonResponse
    {
        // 固定レスポンスを返す（土台）
        return response()->json(['message' => 'ok']);
    }
}
