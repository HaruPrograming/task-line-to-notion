# laravel-app

## 環境
- Docker: `php:8.4-cli` / Laravel 11
- 起動: `docker compose up -d` → `http://localhost:8000`

## アーキテクチャ
- Controller: バリデーション + Service 呼び出しのみ
- ビジネスロジック: Service 層に書く
- レスポンス: `response()->json()` で統一
