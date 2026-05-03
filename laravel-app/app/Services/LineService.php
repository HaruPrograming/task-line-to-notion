<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LineService
{
    private const REPLY_URL = 'https://api.line.me/v2/bot/message/reply';

    private string $channelAccessToken;

    public function __construct()
    {
        $this->channelAccessToken = config('services.line.channel_access_token');
    }

    public function reply(string $replyToken, string $message): void
    {
        Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->channelAccessToken,
        ])->post(self::REPLY_URL, [
            'replyToken' => $replyToken,
            'messages'   => [
                ['type' => 'text', 'text' => $message],
            ],
        ]);
    }
}
