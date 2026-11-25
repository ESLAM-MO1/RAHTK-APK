<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('currency', 3)->default('SAR');
            $table->bigInteger('balance_halala')->default(0);
            $table->bigInteger('held_halala')->default(0);
            $table->string('status', 20)->default('active');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->index(['status']);
        });

        Schema::create('wallet_entries', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->uuid('counterpart_entry_id')->nullable();
            $table->string('type', 30);
            $table->string('direction', 6);
            $table->bigInteger('amount_halala');
            $table->bigInteger('balance_after_halala');
            $table->string('reference_type', 60)->nullable();
            $table->uuid('reference_id')->nullable();
            $table->string('idempotency_key', 80);
            $table->string('description', 190)->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['wallet_id', 'idempotency_key']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['wallet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_entries');
        Schema::dropIfExists('wallets');
    }
};
