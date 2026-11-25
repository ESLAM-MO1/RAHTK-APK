<?php

declare(strict_types=1);

namespace App\Shared\Http;

enum ErrorCode: string
{
    case Unauthenticated = 'unauthenticated';
    case Forbidden = 'forbidden';
    case NotFound = 'not_found';
    case ValidationFailed = 'validation_failed';
    case RateLimited = 'rate_limited';
    case ServerError = 'server_error';
    case ServiceUnavailable = 'service_unavailable';
    case InvalidOtp = 'invalid_otp';
    case OtpExpired = 'otp_expired';
    case OtpAttemptsExceeded = 'otp_attempts_exceeded';
    case OtpCooldown = 'otp_cooldown';
    case InvalidPhone = 'invalid_phone';
    case WalletInsufficientBalance = 'wallet_insufficient_balance';
    case WalletFrozen = 'wallet_frozen';
    case DuplicateRequest = 'duplicate_request';
    case RoleNotGranted = 'role_not_granted';
    case KycRequired = 'kyc_required';
    case PackageLimitReached = 'package_limit_reached';
    case InvalidStateTransition = 'invalid_state_transition';
    case InsufficientStock = 'insufficient_stock';
    case AuctionClosed = 'auction_closed';
    case BidTooLow = 'bid_too_low';
    case ShippingCarrierUnavailable = 'shipping_carrier_unavailable';

    public function status(): int
    {
        return match ($this) {
            self::Unauthenticated => 401,
            self::Forbidden, self::RoleNotGranted, self::KycRequired => 403,
            self::NotFound => 404,
            self::ValidationFailed, self::InvalidPhone => 422,
            self::RateLimited, self::OtpCooldown => 429,
            self::WalletInsufficientBalance,
            self::DuplicateRequest,
            self::InvalidStateTransition,
            self::InsufficientStock,
            self::AuctionClosed,
            self::BidTooLow => 409,
            self::ServiceUnavailable, self::ShippingCarrierUnavailable => 503,
            default => 400,
        };
    }

    public function translation(): string
    {
        $message = __('errors.'.$this->value);

        return is_string($message) ? $message : $this->value;
    }
}
