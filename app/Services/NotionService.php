<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NotionService
{
    // Notion API のベースURL
    private const API_BASE = 'https://api.notion.com/v1';

    // Notion API のバージョン（必須ヘッダー）
    private const API_VERSION = '2022-06-28';

    private string $apiKey;
    private string $databaseId;

    public function __construct()
    {
        // .env から APIキーとデータベースIDを読み込む
        $this->apiKey     = config('services.notion.api_key');
        $this->databaseId = config('services.notion.database_id');
    }

    public function fetchDaily(): array
    {
        $response = Http::withHeaders([
            'Authorization'  => 'Bearer ' . $this->apiKey,
            'Notion-Version' => self::API_VERSION,
        ])->post(self::API_BASE . '/databases/' . $this->databaseId . '/query', (object)[]);

        $pages = $response->json('results', []);

        $todayTasks    = [];
        $tomorrowGoals = [];
        $allTags       = [];

        foreach ($pages as $page) {
            $tagOptions = $page['properties']['tags']['multi_select'] ?? [];
            $tagNames   = array_column($tagOptions, 'name');
            $title      = $page['properties']['title']['title'][0]['plain_text'] ?? '';

            foreach ($tagNames as $tag) {
                if (!in_array($tag, $allTags)) {
                    $allTags[] = $tag;
                }
            }

            if (in_array('今日やること', $tagNames)) {
                $todayTasks[] = $title;
            }

            if (in_array('目標', $tagNames)) {
                $tomorrowGoals[] = $title;
            }
        }

        return [
            'today_tasks'    => $todayTasks,
            'tomorrow_goals' => $tomorrowGoals,
            'tags'           => $allTags,
        ];
    }

    /**
     * Notion データベースにページを作成する
     *
     * @param string $content タイトルに保存する本文
     * @param array  $tags    multi_select に保存するタグ一覧
     */
    public function createPage(string $content, array $tags): void
    {
        // multi_select は [{name: "タグ名"}, ...] の形式が必要
        $multiSelect = array_map(fn($tag) => ['name' => $tag], $tags);

        // Notion API に送るリクエストボディを組み立てる
        $body = [
            // 保存先のデータベースを指定する
            'parent' => [
                'database_id' => $this->databaseId,
            ],
            // ページのプロパティ（カラム）を設定する
            'properties' => [
                // title プロパティに本文を保存する
                'title' => [
                    'title' => [
                        ['text' => ['content' => $content]],
                    ],
                ],
                // tags プロパティにタグを保存する
                'tags' => [
                    'multi_select' => $multiSelect,
                ],
            ],
        ];

        // Notion の /v1/pages に POST リクエストを送る
        Http::withHeaders([
            'Authorization'  => 'Bearer ' . $this->apiKey,
            'Notion-Version' => self::API_VERSION,
        ])->post(self::API_BASE . '/pages', $body);
    }
}
