<?php

declare(strict_types=1);

namespace App\Shared\Http;

use RuntimeException;

final class ModuleException extends RuntimeException
{
    public function __construct(
        public readonly ErrorCode $errorCode,
        public readonly array $details = [],
    ) {
        parent::__construct($errorCode->value);
    }
}
