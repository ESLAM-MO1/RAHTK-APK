<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('phone', 16)->unique();
            $table->timestampTz('phone_verified_at')->nullable();
            $table->string('name', 120)->nullable();
            $table->string('email', 190)->nullable()->unique();
            $table->char('locale', 2)->default('ar');
            $table->string('status', 20)->default('active');
            $table->timestampTz('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->softDeletesTz();

            $table->index(['status', 'created_at']);
        });

        Schema::create('user_roles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 30);
            $table->string('status', 20)->default('pending_kyc');
            $table->timestampTz('approved_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->unique(['user_id', 'role']);
            $table->index(['role', 'status']);
        });

        Schema::create('user_devices', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('platform', 10);
            $table->string('device_name', 120)->nullable();
            $table->string('push_token', 255)->nullable();
            $table->timestampTz('last_seen_at')->nullable();
            $table->timestampTz('revoked_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->index(['user_id', 'revoked_at']);
        });

        Schema::create('otp_requests', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('phone', 16);
            $table->string('code_hash', 64);
            $table->string('purpose', 30);
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestampTz('expires_at');
            $table->timestampTz('consumed_at')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['phone', 'created_at']);
            $table->index(['expires_at']);
        });

        Schema::create('auth_sessions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->uuid('device_id')->nullable();
            $table->string('refresh_token_hash', 64)->unique();
            $table->uuid('family_id');
            $table->timestampTz('expires_at');
            $table->timestampTz('revoked_at')->nullable();
            $table->uuid('replaced_by')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['user_id', 'revoked_at']);
            $table->index(['family_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_sessions');
        Schema::dropIfExists('otp_requests');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('users');
    }
};
