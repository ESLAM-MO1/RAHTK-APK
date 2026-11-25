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
        $phone = (string) PhoneNumber::normalize($request->string('phone')->toString());
        $purpose = $request->string('purpose')->toString();

        $code = $this->otp->request($phone, $purpose, $request->ip());

        $payload = ['phone' => PhoneNumber::mask($phone)];

        if ((bool) config('rahtk.otp.expose')) {
            $payload['code'] = $code;
        }

        return ApiResponse::item($payload);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $phone = (string) PhoneNumber::normalize($request->string('phone')->toString());
        $purpose = $request->string('purpose')->toString();

        $this->otp->verify($phone, $purpose, $request->string('code')->toString());

        $user = User::query()->firstOrCreate(
            ['phone' => $phone],
            [
                'phone_verified_at' => now(),
                'locale' => 'ar',
                'status' => 'active',
            ],
        );

        if ($user->phone_verified_at === null) {
            $user->forceFill(['phone_verified_at' => now()])->save();
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return ApiResponse::item([
            'tokens' => $this->tokens->issue($user),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => PhoneNumber::mask($phone),
                'locale' => $user->locale,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return ApiResponse::item([
            'id' => $user?->id,
            'name' => $user?->name,
            'phone' => $user?->phone,
            'locale' => $user?->locale,
        ]);
    }
}
