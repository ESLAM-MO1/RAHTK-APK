<?php

declare(strict_types=1);

namespace App\Modules\Identity\Application;

use App\Modules\Identity\Domain\Models\AuthSession;
use App\Modules\Identity\Domain\Models\User;
use Illuminate\Support\Str;

final class TokenService
{
    public function issue(User $user): array
    {
        $refresh = Str::random(64);
        $expiresAt = now()->addDays((int) config('rahtk.tokens.refresh_ttl_days'));

        AuthSession::query()->create([
            'user_id' => $user->getKey(),
            'refresh_token_hash' => hash('sha256', $refresh),
            'family_id' => (string) Str::uuid7(),
            'expires_at' => $expiresAt,
        ]);

        $token = $user->createToken(
            'access',
            ['*'],
            now()->addMinutes((int) config('rahtk.tokens.access_ttl_minutes')),
        );

        return [
            'access_token' => $token->plainTextToken,
            'refresh_token' => $refresh,
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }
}
