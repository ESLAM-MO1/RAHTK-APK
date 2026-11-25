<?php

declare(strict_types=1);

namespace App\Modules\Identity\Support;

final class PhoneNumber
{
    public static function normalize(string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        if (str_starts_with($digits, '00966')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '966')) {
            $digits = substr($digits, 3);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) !== 9) {
            return null;
        }

        if (! preg_match('/^5[013456789]\d{7}$/', $digits)) {
            return null;
        }

        return '+966'.$digits;
    }

    public static function mask(string $phone): string
    {
        if (strlen($phone) < 8) {
            return $phone;
        }

        return substr($phone, 0, 4).'****'.substr($phone, -2);
    }
}
