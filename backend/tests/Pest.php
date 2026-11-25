<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->in('Feature', 'Unit');

beforeEach(function (): void {
    Cache::flush();
});

expect()->extend('toBeWithinRange', function (float $min, float $max) {
    return $this->toBeGreaterThanOrEqual($min)->toBeLessThanOrEqual($max);
});
