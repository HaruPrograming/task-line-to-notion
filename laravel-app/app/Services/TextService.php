<?php

namespace App\Services;

class TextService
{
    /**
     * テキストからハッシュタグを除去して本文だけを返す
     *
     * 例: "#task #urgent Docker環境を作る" → "Docker環境を作る"
     */
    public function extractBody(string $text): string
    {
        // "#タグ名" にマッチするパターン（日本語・英数字対応）
        $pattern = '/#[\w\p{L}]+/u';

        // ハッシュタグをすべて空文字に置換する
        $body = preg_replace($pattern, '', $text);

        // 前後の空白・改行を除去して返す
        return trim($body);
    }
}
