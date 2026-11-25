<?php

declare(strict_types=1);

use App\Modules\Identity\Domain\Models\User;
use App\Modules\Identity\Support\PhoneNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('rahtk.otp.expose', true);
});

it('normalizes local and international saudi numbers', function (string $input, string $expected): void {
    expect(PhoneNumber::normalize($input))->toBe($expected);
})->with([
    'local' => ['0551234567', '+966551234567'],
    'with country code' => ['966551234567', '+966551234567'],
    'with plus' => ['+966551234567', '+966551234567'],
    'with double zero' => ['00966551234567', '+966551234567'],
    'with separators' => ['055 123 4567', '+966551234567'],
]);

it('rejects malformed numbers', function (string $input): void {
    expect(PhoneNumber::normalize($input))->toBeNull();
})->with([
    'too short' => ['055123'],
    'wrong prefix' => ['05512345678'],
    'landline' => ['0112345678'],
    'letters' => ['not-a-number'],
]);

it('masks the middle of the number', function (): void {
    expect(PhoneNumber::mask('+966551234567'))->toBe('+966****67');
});

it('sends an otp and stores only its hash', function (): void {
    $response = $this->postJson('/api/v1/auth/otp', [
        'phone' => '0551234567',
        'purpose' => 'login',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.phone', '+966****67')
        ->assertJsonStructure(['data' => ['phone', 'code']]);

    $this->assertDatabaseCount('otp_requests', 1);
    $this->assertDatabaseMissing('otp_requests', ['code_hash' => $response->json('data.code')]);
});

it('rejects a phone number that cannot be normalized', function (): void {
    $this->postJson('/api/v1/auth/otp', [
        'phone' => '123',
        'purpose' => 'login',
    ])->assertStatus(422)
        ->assertJsonPath('error.code', 'validation_failed')
        ->assertJsonStructure(['error' => ['details' => ['phone']]]);
});

it('enforces the request cooldown', function (): void {
    $payload = ['phone' => '0551234567', 'purpose' => 'login'];

    $this->postJson('/api/v1/auth/otp', $payload)->assertOk();
    $this->postJson('/api/v1/auth/otp', $payload)
        ->assertStatus(429)
        ->assertJsonPath('error.code', 'otp_cooldown');
});

it('issues tokens for a valid otp', function (): void {
    $code = $this->postJson('/api/v1/auth/otp', [
        'phone' => '0551234567',
        'purpose' => 'login',
    ])->json('data.code');

    $response = $this->postJson('/api/v1/auth/verify', [
        'phone' => '0551234567',
        'purpose' => 'login',
        'code' => $code,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'tokens' => ['access_token', 'refresh_token', 'expires_at'],
                'user' => ['id', 'phone', 'locale'],
            ],
        ]);

    expect($response->json('data.user.phone'))->toBe('+966****67');
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('auth_sessions', 1);
});

it('rejects a wrong otp and counts the attempt', function (): void {
    $this->postJson('/api/v1/auth/otp', [
        'phone' => '0551234567',
        'purpose' => 'login',
    ])->assertOk();

    $this->postJson('/api/v1/auth/verify', [
        'phone' => '0551234567',
        'purpose' => 'login',
        'code' => '000000',
    ])->assertStatus(400)
        ->assertJsonPath('error.code', 'invalid_otp');

    expect(DB::table('otp_requests')->value('attempts'))->toBe(1);
});

it('consumes an otp so it cannot be replayed', function (): void {
    $code = $this->postJson('/api/v1/auth/otp', [
        'phone' => '0551234567',
        'purpose' => 'login',
    ])->json('data.code');

    $payload = ['phone' => '0551234567', 'purpose' => 'login', 'code' => $code];

    $this->postJson('/api/v1/auth/verify', $payload)->assertOk();
    $this->postJson('/api/v1/auth/verify', $payload)
        ->assertStatus(400)
        ->assertJsonPath('error.code', 'invalid_otp');
});

it('rejects an expired otp', function (): void {
    $code = $this->postJson('/api/v1/auth/otp', [
        'phone' => '0551234567',
        'purpose' => 'login',
    ])->json('data.code');

    DB::table('otp_requests')->update(['expires_at' => now()->subMinute()]);

    $this->postJson('/api/v1/auth/verify', [
        'phone' => '0551234567',
        'purpose' => 'login',
        'code' => $code,
    ])->assertStatus(400)
        ->assertJsonPath('error.code', 'otp_expired');
});

it('returns the authenticated profile', function (): void {
    $user = User::query()->create([
        'phone' => '+966551234567',
        'name' => 'سالم',
        'locale' => 'ar',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.phone', '+966551234567');
});

it('guards the profile endpoint', function (): void {
    $this->getJson('/api/v1/me')
        ->assertUnauthorized()
        ->assertJsonPath('error.code', 'unauthenticated');
});
