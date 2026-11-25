<?php

declare(strict_types=1);

use App\Modules\Wallet\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/v1/wallet', [WalletController::class, 'show'])->name('v1.wallet.show');
    Route::get('/v1/wallet/entries', [WalletController::class, 'entries'])->name('v1.wallet.entries');
});
