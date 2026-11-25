<?php

declare(strict_types=1);

namespace App\Modules\Identity\Http\Controllers;

use App\Modules\Identity\Application\OtpService;
use App\Modules\Identity\Application\TokenService;
use App\Modules\Identity\Domain\Models\User;
use App\Modules\Identity\Http\Requests\RequestOtpRequest;
use App\Modules\Identity\Http\Requests\VerifyOtpRequest;
use App\Modules\Identity\Support\PhoneNumber;
use App\Shared\Http\ApiResponse;
use App\Shared\Http\ErrorCode;
use App\Shared\Http\ModuleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController
{
    public function __construct(
        private readonly OtpService $otp,
        private readonly TokenService $tokens,
    ) {}

    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {
        $phone = PhoneNumber::normalize($request->string('phone')->toString());

        if ($phone === null) {
            throw new ModuleException(ErrorCode::InvalidPhone);
        }

        $code = $this->otp->request($phone, $request->string('purpose')->toString(), $request->ip());

        $payload = ['phone' => PhoneNumber::mask($phone)];

        if ((bool) config('rahtk.otp.expose')) {
            $payload['code'] = $code;
        }

        return ApiResponse::item($payload);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $phone = PhoneNumber::normalize($request->string('phone')->toString());

        if ($phone === null) {
            throw new ModuleException(ErrorCode::InvalidPhone);
        }

        $this->otp->verify($phone, $request->string('purpose')->toString(), $request->string('code')->toString());

        $user = User::query()->where('phone', $phone)->first();

        if (! $user instanceof User) {
            $user = User::query()->create([
                'phone' => $phone,
                'phone_verified_at' => now(),
                'locale' => 'ar',
                'status' => 'active',
            ]);
        }

        $user->forceFill([
            'phone_verified_at' => $user->getAttribute('phone_verified_at') ?? now(),
            'last_login_at' => now(),
        ])->save();

        return ApiResponse::item([
            'tokens' => $this->tokens->issue($user),
            'user' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'phone' => PhoneNumber::mask($phone),
                'locale' => $user->locale,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new ModuleException(ErrorCode::Unauthenticated);
        }

        return ApiResponse::item([
            'id' => $user->getKey(),
            'name' => $user->name,
            'phone' => $user->phone,
            'locale' => $user->locale,
        ]);
    }
}
