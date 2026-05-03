<?php

namespace App\Http\Controllers;

use App\Services\LineService;
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
        NotionService $notionService,
        LineService $lineService
    ): JsonResponse {
        Log::info('LINE Request', $request->all());

        $text       = $request->input('events.0.message.text');
        $replyToken = $request->input('events.0.replyToken');

        if ($text === null) {
            return response()->json(['status' => 'ok']);
        }

        $content = $textService->extractBody($text);
        $tags    = $textService->extractTags($text);
        $notionService->createPage($content, $tags);

        // Notion保存後にLINEへ返信
        if ($replyToken) {
            $tagText      = implode(' ', array_map(fn($t) => "#{$t}", $tags));
            $replyMessage = "タグ: {$tagText}\n内容: {$content}\n保存しました！";
            $lineService->reply($replyToken, $replyMessage);
        }

        return response()->json([
            'content' => $content,
            'tags'    => $tags,
        ]);
    }
}
