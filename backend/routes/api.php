<?php

declare(strict_types=1);

use App\Modules\Identity\Http\Controllers\AuthController;
use App\Shared\Http\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::get('/v1/ping', function () {
    return ApiResponse::item([
        'status' => 'ok',
        'server_time' => now()->toIso8601String(),
    ]);
})->name('v1.ping');

Route::prefix('v1/auth')->group(function (): void {
    Route::post('/otp', [AuthController::class, 'requestOtp'])
        ->middleware('throttle:otp-request')
        ->name('v1.auth.otp.request');

    Route::post('/verify', [AuthController::class, 'verifyOtp'])
        ->middleware('throttle:otp-verify')
        ->name('v1.auth.otp.verify');
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/v1/me', [AuthController::class, 'me'])->name('v1.me');
});

require __DIR__.'/wallet.php';
