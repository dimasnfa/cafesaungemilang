<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MidtransWebhookController;

// API untuk mendapatkan data user (Contoh API dengan Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ===================
// MIDTRANS WEBHOOK
// ===================
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handleWebhook']);