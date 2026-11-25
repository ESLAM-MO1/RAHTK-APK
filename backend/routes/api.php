<?php

declare(strict_types=1);

use App\Shared\Http\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::get('/v1/ping', function () {
    return ApiResponse::item([
        'status' => 'ok',
        'server_time' => now()->toIso8601String(),
    ]);
})->name('v1.ping');
