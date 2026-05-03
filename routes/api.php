<?php

use App\Http\Controllers\CronController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| ここに API のルートを定義します。
| このファイルは bootstrap/app.php で読み込まれます。
|
*/

// LINE Webhook 受信エンドポイント
// POST /api/webhook → WebhookController@handle
Route::post('/webhook', [WebhookController::class, 'handle']);

Route::get('/cron/daily', [CronController::class, 'daily']);
