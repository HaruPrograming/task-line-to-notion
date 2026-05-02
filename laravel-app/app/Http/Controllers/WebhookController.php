<?php

namespace App\Http\Controllers;

use App\Services\TextService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * LINE からの Webhook リクエストを受け取る
     *
     * POST /api/webhook
     */
    public function handle(Request $request, TextService $textService): JsonResponse
    {
        // リクエストJSONから "text" フィールドを取得する
        $text = $request->input('text', '');

        // ハッシュタグを除去した本文を取得する
        $content = $textService->extractBody($text);

        // 本文を返す
        return response()->json(['content' => $content]);
    }
}
