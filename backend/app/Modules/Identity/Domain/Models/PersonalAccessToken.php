<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\PersonalAccessToken as SanctumToken;

final class PersonalAccessToken extends SanctumToken
{
    use HasUuids;

    protected $table = 'personal_access_tokens';

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'abilities' => 'json',
            'last_used_at' => 'immutable_datetime',
            'expires_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }
}
