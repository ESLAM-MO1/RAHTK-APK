<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Domain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class WalletEntry extends Model
{
    use HasUuids;

    protected $table = 'wallet_entries';

    public $timestamps = false;

    protected $fillable = [
        'wallet_id',
        'counterpart_entry_id',
        'type',
        'direction',
        'amount_halala',
        'balance_after_halala',
        'reference_type',
        'reference_id',
        'idempotency_key',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount_halala' => 'integer',
            'balance_after_halala' => 'integer',
            'created_at' => 'immutable_datetime',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
