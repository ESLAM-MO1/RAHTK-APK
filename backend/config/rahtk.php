<?php

declare(strict_types=1);

return [

    'otp' => [
        'length' => 6,
        'ttl_minutes' => 5,
        'max_attempts' => 5,
        'cooldown_seconds' => 60,
        'expose' => (bool) env('OTP_EXPOSE', false),
    ],

    'tokens' => [
        'access_ttl_minutes' => 30,
        'refresh_ttl_days' => 30,
    ],

    'wallet' => [
        'currency' => 'SAR',
        'max_balance_halala' => 100000000000,
    ],

];
