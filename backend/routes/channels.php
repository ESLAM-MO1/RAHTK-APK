<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}', function ($user, string $userId): bool {
    return (string) $user->getKey() === $userId;
});

Broadcast::channel('tenant.{tenantId}', function ($user, string $tenantId): bool {
    return (string) $user->merchant_id === $tenantId;
});
