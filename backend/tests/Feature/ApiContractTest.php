<?php

declare(strict_types=1);

use App\Shared\Http\ApiResponse;
use App\Shared\Http\ErrorCode;

it('reports service health', function (): void {
    $this->getJson('/health')->assertOk();
});

it('returns the api banner', function (): void {
    $this->getJson('/api/v1/ping')
        ->assertOk()
        ->assertJsonPath('data.status', 'ok')
        ->assertJsonStructure(['data' => ['status', 'server_time']]);
});

it('rejects unknown api routes with the shared error shape', function (): void {
    $this->getJson('/api/v1/missing')
        ->assertNotFound()
        ->assertJsonPath('error.code', ErrorCode::NotFound->value)
        ->assertJsonStructure(['error' => ['code', 'message']]);
});

it('wraps single resources under data', function (): void {
    $response = ApiResponse::item(['id' => 'abc']);

    expect($response->getStatusCode())->toBe(200);
    expect(json_decode((string) $response->getContent(), true))->toBe(['data' => ['id' => 'abc']]);
});

it('wraps collections with meta', function (): void {
    $response = ApiResponse::collection([['id' => 'abc']], ['next_cursor' => null]);

    expect(json_decode((string) $response->getContent(), true))
        ->toBe(['data' => [['id' => 'abc']], 'meta' => ['next_cursor' => null]]);
});

it('marks created responses with 201', function (): void {
    expect(ApiResponse::created(['id' => 'abc'])->getStatusCode())->toBe(201);
});

it('sends an empty body for no content', function (): void {
    $response = ApiResponse::noContent();

    expect($response->getStatusCode())->toBe(204);
    expect((string) $response->getContent())->toBe('');
});
