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
        // リクエストJSONから "text" フィールドを取得する
        $text = $request->input('text');

        // 受け取ったテキストをそのまま返す
        return response()->json(['received' => $text]);
    }
}
