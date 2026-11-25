<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Application;

use App\Modules\Wallet\Domain\Models\Wallet;
use App\Modules\Wallet\Domain\Models\WalletEntry;
use App\Shared\Http\ErrorCode;
use App\Shared\Http\ModuleException;
use Illuminate\Support\Facades\DB;

final class LedgerService
{
    public function walletFor(string $userId, string $currency = 'SAR'): Wallet
    {
        $wallet = Wallet::query()->where('user_id', $userId)->first();

        if ($wallet instanceof Wallet) {
            return $wallet;
        }

        return Wallet::query()->create([
            'user_id' => $userId,
            'currency' => $currency,
            'balance_halala' => 0,
            'held_halala' => 0,
            'status' => 'active',
        ]);
    }

    public function credit(
        Wallet $wallet,
        int $amountHalala,
        string $type,
        string $idempotencyKey,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?string $description = null,
    ): WalletEntry {
        $this->assertAmount($amountHalala);

        return $this->apply(
            $wallet,
            $amountHalala,
            $type,
            $idempotencyKey,
            $referenceType,
            $referenceId,
            $description,
        );
    }

    public function debit(
        Wallet $wallet,
        int $amountHalala,
        string $type,
        string $idempotencyKey,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?string $description = null,
    ): WalletEntry {
        $this->assertAmount($amountHalala);

        return $this->apply(
            $wallet,
            -$amountHalala,
            $type,
            $idempotencyKey,
            $referenceType,
            $referenceId,
            $description,
        );
    }

    private function apply(
        Wallet $wallet,
        int $signedHalala,
        string $type,
        string $idempotencyKey,
        ?string $referenceType,
        ?string $referenceId,
        ?string $description,
    ): WalletEntry {
        return DB::transaction(function () use (
            $wallet,
            $signedHalala,
            $type,
            $idempotencyKey,
            $referenceType,
            $referenceId,
            $description,
        ): WalletEntry {
            $locked = Wallet::query()->whereKey($wallet->getKey())->lockForUpdate()->firstOrFail();

            $existing = WalletEntry::query()
                ->where('wallet_id', $locked->getKey())
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing !== null) {
                return $existing;
            }

            if (! $locked->isActive()) {
                throw new ModuleException(ErrorCode::WalletFrozen);
            }

            $newBalance = $locked->balance_halala + $signedHalala;

            if ($newBalance < 0) {
                throw new ModuleException(ErrorCode::WalletInsufficientBalance, [
                    'required' => $signedHalala,
                    'available' => $locked->availableHalala(),
                ]);
            }

            $locked->forceFill(['balance_halala' => $newBalance])->save();

            $entry = WalletEntry::query()->create([
                'wallet_id' => $locked->getKey(),
                'type' => $type,
                'direction' => $signedHalala >= 0 ? 'credit' : 'debit',
                'amount_halala' => abs($signedHalala),
                'balance_after_halala' => $newBalance,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'idempotency_key' => $idempotencyKey,
                'description' => $description,
            ]);

            $wallet->refresh();

            return $entry;
        });
    }

    private function assertAmount(int $amountHalala): void
    {
        if ($amountHalala <= 0) {
            throw new ModuleException(ErrorCode::ValidationFailed, [
                'amount' => ['amount_must_be_positive'],
            ]);
        }

        if ($amountHalala > (int) config('rahtk.wallet.max_balance_halala')) {
            throw new ModuleException(ErrorCode::ValidationFailed, [
                'amount' => ['amount_too_large'],
            ]);
        }
    }
}
