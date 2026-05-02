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

        // "#タグ名" にマッチするパターンを定義する
        // # の後に続く英数字・アンダースコア・日本語などをキャプチャする
        $pattern = '/#([\w\p{L}]+)/u';

        // テキスト内の全ハッシュタグを配列で取得する
        // $matches[1] に # を除いたタグ名だけが入る
        preg_match_all($pattern, $text, $matches);

        // 抽出したタグ一覧を返す
        return response()->json(['tags' => $matches[1]]);
    }
}
