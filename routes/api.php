<?php

use App\Http\Controllers\Api\Webhooks\PrestaShopWebhookController;
use App\Http\Controllers\Api\Webhooks\InPostWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Webhook routes
Route::post('/webhooks/prestashop', [PrestaShopWebhookController::class, 'handle']);
Route::post('/webhooks/inpost', [InPostWebhookController::class, 'handle']);
