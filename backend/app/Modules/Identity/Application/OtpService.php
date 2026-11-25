<?php

declare(strict_types=1);

namespace App\Modules\Identity\Application;

use App\Modules\Identity\Domain\Models\OtpRequest;
use App\Shared\Http\ErrorCode;
use App\Shared\Http\ModuleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class OtpService
{
    public function request(string $phone, string $purpose, ?string $ip): string
    {
        $cooldown = (int) config('rahtk.otp.cooldown_seconds');

        $recent = OtpRequest::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('created_at', '>', now()->subSeconds($cooldown))
            ->exists();

        if ($recent) {
            throw new ModuleException(ErrorCode::OtpCooldown);
        }

        $length = (int) config('rahtk.otp.length');
        $code = str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);

        OtpRequest::query()->create([
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'purpose' => $purpose,
            'attempts' => 0,
            'expires_at' => now()->addMinutes((int) config('rahtk.otp.ttl_minutes')),
            'ip' => $ip,
        ]);

        return $code;
    }

    public function verify(string $phone, string $purpose, string $code): OtpRequest
    {
        $request = DB::transaction(function () use ($phone, $purpose): ?OtpRequest {
            return OtpRequest::query()
                ->where('phone', $phone)
                ->where('purpose', $purpose)
                ->whereNull('consumed_at')
                ->latest('created_at')
                ->lockForUpdate()
                ->first();
        });

        if ($request === null) {
            throw new ModuleException(ErrorCode::InvalidOtp);
        }

        if ($request->isExpired()) {
            throw new ModuleException(ErrorCode::OtpExpired);
        }

        $maxAttempts = (int) config('rahtk.otp.max_attempts');

        if ($request->attempts >= $maxAttempts) {
            throw new ModuleException(ErrorCode::OtpAttemptsExceeded);
        }

        if (! Hash::check($code, $request->code_hash)) {
            $request->increment('attempts');

            throw new ModuleException(ErrorCode::InvalidOtp);
        }

        $request->forceFill(['consumed_at' => now()])->save();

        return $request;
    }
}
