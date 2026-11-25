<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Http\Controllers;

use App\Modules\Identity\Domain\Models\User;
use App\Modules\Wallet\Application\LedgerService;
use App\Modules\Wallet\Domain\Models\WalletEntry;
use App\Shared\Http\ApiResponse;
use App\Shared\Http\ErrorCode;
use App\Shared\Http\ModuleException;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class WalletController
{
    public function __construct(private readonly LedgerService $ledger) {}

    public function show(Request $request): JsonResponse
    {
        $wallet = $this->ledger->walletFor($this->userId($request));

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
        $wallet = $this->ledger->walletFor($this->userId($request));

        $entries = [];

        $records = WalletEntry::query()
            ->where('wallet_id', $wallet->getKey())
            ->latest('created_at')
            ->limit(50)
            ->get();

        foreach ($records as $entry) {
            $createdAt = $entry->getAttribute('created_at');

            $entries[] = [
                'id' => $entry->getKey(),
                'type' => $entry->type,
                'direction' => $entry->direction,
                'amount_halala' => $entry->amount_halala,
                'balance_after_halala' => $entry->balance_after_halala,
                'description' => $entry->description,
                'created_at' => $createdAt instanceof CarbonInterface
                    ? $createdAt->toIso8601String()
                    : null,
            ];
        }

        return ApiResponse::collection($entries, ['count' => count($entries)]);
    }

    private function userId(Request $request): string
    {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new ModuleException(ErrorCode::Unauthenticated);
        }

        return (string) $user->getAuthIdentifier();
    }
}
