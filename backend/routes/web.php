<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function (): array {
    return [
        'name' => 'Rahtk API',
        'version' => 'v1',
    ];
});
