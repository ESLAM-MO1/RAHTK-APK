<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function item(mixed $data, int $status = 200): JsonResponse
    {
        return new JsonResponse(['data' => $data], $status);
    }

    public static function collection(iterable $items, array $meta = []): JsonResponse
    {
        return new JsonResponse([
            'data' => $items,
            'meta' => $meta,
        ]);
    }

    public static function created(mixed $data): JsonResponse
    {
        return self::item($data, 201);
    }

    public static function noContent(): JsonResponse
    {
        return new JsonResponse(null, 204);
    }
}
