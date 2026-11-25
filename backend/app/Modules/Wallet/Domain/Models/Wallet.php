<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Wallet extends Model
{
    use HasUuids;

    protected $table = 'wallets';

    protected $fillable = [
        'user_id',
        'currency',
        'balance_halala',
        'held_halala',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'balance_halala' => 'integer',
            'held_halala' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(WalletEntry::class);
    }

    public function availableHalala(): int
    {
        return $this->balance_halala - $this->held_halala;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
