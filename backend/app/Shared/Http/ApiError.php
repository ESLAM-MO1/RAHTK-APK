<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Illuminate\Http\JsonResponse;

final class ApiError
{
    public static function respond(
        ErrorCode $code,
        ?string $message = null,
        ?int $status = null,
        array $details = [],
    ): JsonResponse {
        $payload = [
            'error' => [
                'code' => $code->value,
                'message' => $message ?? $code->translation(),
            ],
        ];

        if ($details !== []) {
            $payload['error']['details'] = $details;
        }

        return new JsonResponse($payload, $status ?? $code->status());
    }
}
