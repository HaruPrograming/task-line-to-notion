<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineService
{
    private const REPLY_URL = 'https://api.line.me/v2/bot/message/reply';
    private const PUSH_URL  = 'https://api.line.me/v2/bot/message/push';

    private string $channelAccessToken;
    private string $notionUrl;

    public function __construct()
    {
        $this->channelAccessToken = config('services.line.channel_access_token');
        $this->notionUrl          = config('services.notion.page_url') ?? '';
    }

    public function formatDailyMessage(array $data): string
    {
        $lines = [];

        $lines[] = '【今日のタスク】';
        if (!empty($data['today_tasks'])) {
            foreach ($data['today_tasks'] as $task) {
                $lines[] = '・' . $task;
            }
        } else {
            $lines[] = '・なし';
        }

        $lines[] = '';

        $lines[] = '【昨日の目標】';
        if (!empty($data['tomorrow_goals'])) {
            foreach ($data['tomorrow_goals'] as $goal) {
                $lines[] = '・' . $goal;
            }
        } else {
            $lines[] = '・なし';
        }

        $lines[] = '';

        $tagList = !empty($data['tags'])
            ? implode(' ', array_map(fn($t) => "#{$t}", $data['tags']))
            : 'なし';
        $lines[] = '登録済みタグ: ' . $tagList;

        return implode("\n", $lines);
    }

    public function push(string $userId, string $message): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->channelAccessToken,
        ])->post(self::PUSH_URL, [
            'to'       => $userId,
            'messages' => [
                ['type' => 'text', 'text' => $message],
            ],
        ]);

        if ($response->successful()) {
            Log::info('LINE push sent', ['userId' => $userId]);
        } else {
            Log::error('LINE push failed', ['status' => $response->status(), 'body' => $response->body()]);
        }
    }

    public function pushWithNotionLink(string $userId, string $message): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->channelAccessToken,
        ])->post(self::PUSH_URL, [
            'to'       => $userId,
            'messages' => [$this->buildFlexMessage($message)],
        ]);

        if ($response->successful()) {
            Log::info('LINE push sent', ['userId' => $userId]);
        } else {
            Log::error('LINE push failed', ['status' => $response->status(), 'body' => $response->body()]);
        }
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

    public function replyWithNotionLink(string $replyToken, string $message): void
    {
        Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->channelAccessToken,
        ])->post(self::REPLY_URL, [
            'replyToken' => $replyToken,
            'messages'   => [$this->buildFlexMessage($message)],
        ]);
    }

    private function buildFlexMessage(string $message): array
    {
        return [
            'type'     => 'flex',
            'altText'  => $message,
            'contents' => [
                'type' => 'bubble',
                'body' => [
                    'type'     => 'box',
                    'layout'   => 'vertical',
                    'spacing'  => 'sm',
                    'contents' => [
                        [
                            'type' => 'text',
                            'text' => $message,
                            'wrap' => true,
                        ],
                        [
                            'type'   => 'text',
                            'text'   => 'Notionで確認 →',
                            'color'  => '#1DB446',
                            'size'   => 'sm',
                            'action' => [
                                'type'  => 'uri',
                                'label' => 'Notionで確認',
                                'uri'   => $this->notionUrl,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
