<?php

declare(strict_types=1);

namespace App\Modules\Identity\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class AuthSession extends Model
{
    use HasUuids;

    protected $table = 'auth_sessions';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'device_id',
        'refresh_token_hash',
        'family_id',
        'expires_at',
        'revoked_at',
        'replaced_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
        ];
    }
}
