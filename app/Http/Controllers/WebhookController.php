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
        Log::channel('single')->info('[WEBHOOK] LINE Request', $request->all());

        foreach ($request->input('events', []) as $event) {
            Log::channel('single')->info('[WEBHOOK] EVENT', $event);

            if (isset($event['source']['userId'])) {
                Log::channel('single')->info('[WEBHOOK] USER_ID', [
                    'userId' => $event['source']['userId'],
                ]);
            }
        }

        $text       = $request->input('events.0.message.text');
        $replyToken = $request->input('events.0.replyToken');
        $userId     = $request->input('events.0.source.userId');

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
            $lineService->replyWithNotionLink($replyToken, $replyMessage);
        }

        return response()->json([
            'content' => $content,
            'tags'    => $tags,
        ]);
    }
}
