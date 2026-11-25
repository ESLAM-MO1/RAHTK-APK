<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Http\Controllers;

use App\Modules\Wallet\Application\LedgerService;
use App\Modules\Wallet\Domain\Models\WalletEntry;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WalletController
{
    public function __construct(private readonly LedgerService $ledger) {}

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $wallet = $this->ledger->walletFor((string) $user->getAuthIdentifier());

        return ApiResponse::item([
            'currency' => $wallet->currency,
            'balance_halala' => $wallet->balance_halala,
            'held_halala' => $wallet->held_halala,
            'available_halala' => $wallet->availableHalala(),
            'status' => $wallet->status,
        ]);
    }

    public function entries(Request $request): JsonResponse
    {
        $user = $request->user();
        $wallet = $this->ledger->walletFor((string) $user->getAuthIdentifier());

        $entries = WalletEntry::query()
            ->where('wallet_id', $wallet->getKey())
            ->latest('created_at')
            ->limit(50)
            ->get()
            ->map(fn (WalletEntry $entry): array => [
                'id' => $entry->id,
                'type' => $entry->type,
                'direction' => $entry->direction,
                'amount_halala' => $entry->amount_halala,
                'balance_after_halala' => $entry->balance_after_halala,
                'description' => $entry->description,
                'created_at' => $entry->created_at?->toIso8601String(),
            ])
            ->all();

        return ApiResponse::collection($entries, ['count' => count($entries)]);
    }
}
