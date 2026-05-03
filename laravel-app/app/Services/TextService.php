<?php

namespace App\Services;

class TextService
{
    // "#タグ名" にマッチするパターン（日本語・英数字対応）
    private const HASHTAG_PATTERN = '/#([\w\p{L}]+)/u';

    /**
     * テキストからハッシュタグを除去して本文だけを返す
     *
     * 例: "#task #urgent Docker環境を作る" → "Docker環境を作る"
     */
    public function extractBody(string $text): string
    {
        // ハッシュタグをすべて空文字に置換する
        $body = preg_replace(self::HASHTAG_PATTERN, '', $text);

        // 前後の空白・改行を除去して返す
        return trim($body);
    }

    /**
     * テキストからハッシュタグを配列で返す
     *
     * 例: "#task #urgent Docker環境を作る" → ["task", "urgent"]
     */
    public function extractTags(string $text): array
    {
        // テキスト内の全ハッシュタグを抽出する
        // $matches[1] に # を除いたタグ名だけが入る
        preg_match_all(self::HASHTAG_PATTERN, $text, $matches);

        return $matches[1];
    }
}
