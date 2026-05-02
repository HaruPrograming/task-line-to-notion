<?php

namespace App\Http\Controllers;

use App\Services\NotionService;
use App\Services\TextService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * LINE からの Webhook リクエストを受け取る
     *
     * POST /api/webhook
     */
    public function handle(
        Request $request,
        TextService $textService,
        NotionService $notionService
    ): JsonResponse {
        // LINEからのリクエスト全体ログ
        Log::info('LINE Request', $request->all());

        // LINE Webhook形式: events[0].message.text からテキストを取得
        $text = $request->input('events.0.message.text');

        // textが取得できない場合はスキップ（LINEは200必須のため即返却）
        if ($text === null) {
            return response()->json(['status' => 'ok']);
        }

        // タグ抽出・本文抽出・Notion保存
        $content = $textService->extractBody($text);
        $tags    = $textService->extractTags($text);
        $notionService->createPage($content, $tags);

        return response()->json([
            'content' => $content,
            'tags'    => $tags,
        ]);
    }
}
