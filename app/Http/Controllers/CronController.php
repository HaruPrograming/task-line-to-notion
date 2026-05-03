<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CronController extends Controller
{
    public function daily(): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }
}
