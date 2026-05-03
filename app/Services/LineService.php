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
            ? implode(' ', array_map(fn($t) => '・' . $t, $data['tags']))
            : '・なし';
        $lines[] = '【登録タグ一覧】 ' . $tagList;

        return implode("\n", $lines);
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
