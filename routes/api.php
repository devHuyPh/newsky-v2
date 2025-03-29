<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\GHNWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/ghn', [GHNWebhookController::class, 'handleWebhook']);

