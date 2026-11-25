<?php

declare(strict_types=1);

use App\Shared\Http\ApiError;
use App\Shared\Http\ErrorCode;

it('builds an error payload with details', function (): void {
    $response = ApiError::respond(
        ErrorCode::WalletInsufficientBalance,
        details: ['required' => '12.00', 'available' => '8.50'],
    );

    expect($response->getStatusCode())->toBe(409);

    $payload = json_decode((string) $response->getContent(), true);

    expect($payload['error']['code'])->toBe('wallet_insufficient_balance');
    expect($payload['error']['message'])->toBe(__('errors.wallet_insufficient_balance'));
    expect($payload['error']['details']['required'])->toBe('12.00');
});

it('omits the details key when none are provided', function (): void {
    $response = ApiError::respond(ErrorCode::NotFound);

    $payload = json_decode((string) $response->getContent(), true);

    expect($payload['error'])->not->toHaveKey('details');
    expect($response->getStatusCode())->toBe(404);
});

it('maps every error code to a client error status', function (ErrorCode $code): void {
    expect($code->status())->toBeGreaterThanOrEqual(400);
})->with(ErrorCode::cases());

it('translates every error code in both locales', function (ErrorCode $code, string $locale): void {
    app()->setLocale($locale);

    expect($code->translation())->not->toBe('errors.'.$code->value);
})->with(ErrorCode::cases())->with(['ar', 'en']);
