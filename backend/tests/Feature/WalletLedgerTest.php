<?php

declare(strict_types=1);

use App\Modules\Identity\Domain\Models\User;
use App\Modules\Wallet\Application\LedgerService;
use App\Modules\Wallet\Domain\Models\Wallet;
use App\Shared\Http\ErrorCode;
use App\Shared\Http\ModuleException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeUser(): User
{
    return User::query()->create([
        'phone' => '+96655123'.random_int(1000, 9999),
        'locale' => 'ar',
        'status' => 'active',
    ]);
}

it('creates a wallet on first access', function (): void {
    $user = makeUser();
    $service = app(LedgerService::class);

    $wallet = $service->walletFor($user->id);

    expect($wallet)->toBeInstanceOf(Wallet::class)
        ->and($wallet->balance_halala)->toBe(0)
        ->and($wallet->currency)->toBe('SAR');

    expect($service->walletFor($user->id)->id)->toBe($wallet->id);
});

it('credits and debits keeping the balance consistent', function (): void {
    $service = app(LedgerService::class);
    $wallet = $service->walletFor(makeUser()->id);

    $service->credit($wallet, 50_000, 'top_up', 'key-1');
    $service->debit($wallet, 20_000, 'purchase', 'key-2');

    $fresh = $wallet->fresh();

    expect($fresh?->balance_halala)->toBe(30_000);
    expect($fresh?->availableHalala())->toBe(30_000);
});

it('records the balance after each entry', function (): void {
    $service = app(LedgerService::class);
    $wallet = $service->walletFor(makeUser()->id);

    $service->credit($wallet, 10_000, 'top_up', 'key-a');
    $entry = $service->credit($wallet, 5_000, 'top_up', 'key-b');

    expect($entry->balance_after_halala)->toBe(15_000)
        ->and($entry->direction)->toBe('credit')
        ->and($entry->amount_halala)->toBe(5_000);
});

it('is idempotent for a repeated key', function (): void {
    $service = app(LedgerService::class);
    $wallet = $service->walletFor(makeUser()->id);

    $first = $service->credit($wallet, 10_000, 'top_up', 'same-key');
    $second = $service->credit($wallet, 10_000, 'top_up', 'same-key');

    expect($second->id)->toBe($first->id);
    expect($wallet->fresh()?->balance_halala)->toBe(10_000);
});

it('refuses to overdraw', function (): void {
    $service = app(LedgerService::class);
    $wallet = $service->walletFor(makeUser()->id);

    $service->credit($wallet, 5_000, 'top_up', 'seed');

    try {
        $service->debit($wallet, 7_000, 'purchase', 'too-much');
        $this->fail('Expected insufficient balance');
    } catch (ModuleException $exception) {
        expect($exception->errorCode)->toBe(ErrorCode::WalletInsufficientBalance);
        expect($exception->details)->toHaveKeys(['required', 'available']);
    }

    expect($wallet->fresh()?->balance_halala)->toBe(5_000);
    $this->assertDatabaseMissing('wallet_entries', ['idempotency_key' => 'too-much']);
});

it('refuses a frozen wallet', function (): void {
    $service = app(LedgerService::class);
    $wallet = $service->walletFor(makeUser()->id);
    $wallet->forceFill(['status' => 'frozen'])->save();

    try {
        $service->credit($wallet, 1_000, 'top_up', 'frozen');
        $this->fail('Expected a frozen wallet rejection');
    } catch (ModuleException $exception) {
        expect($exception->errorCode)->toBe(ErrorCode::WalletFrozen);
    }
});

it('rejects non positive amounts', function (int $amount): void {
    $service = app(LedgerService::class);
    $wallet = $service->walletFor(makeUser()->id);

    expect(fn () => $service->credit($wallet, $amount, 'top_up', 'bad-' . $amount))
        ->toThrow(ModuleException::class);
})->with([0, -100]);

it('exposes the wallet over the api', function (): void {
    $user = makeUser();
    $service = app(LedgerService::class);

    $service->credit($service->walletFor($user->id), 125_075, 'top_up', 'api-seed');

    $this->actingAs($user)
        ->getJson('/api/v1/wallet')
        ->assertOk()
        ->assertJsonPath('data.balance_halala', 125_075)
        ->assertJsonPath('data.currency', 'SAR')
        ->assertJsonPath('data.status', 'active');
});

it('lists wallet entries newest first', function (): void {
    $user = makeUser();
    $service = app(LedgerService::class);
    $wallet = $service->walletFor($user->id);

    $service->credit($wallet, 1_000, 'top_up', 'one');
    $service->credit($wallet, 2_000, 'top_up', 'two');

    $response = $this->actingAs($user)->getJson('/api/v1/wallet/entries');

    $response->assertOk()
        ->assertJsonPath('meta.count', 2)
        ->assertJsonPath('data.0.amount_halala', 2_000)
        ->assertJsonPath('data.1.amount_halala', 1_000);
});

it('guards wallet endpoints', function (): void {
    $this->getJson('/api/v1/wallet')->assertUnauthorized();
    $this->getJson('/api/v1/wallet/entries')->assertUnauthorized();
});
