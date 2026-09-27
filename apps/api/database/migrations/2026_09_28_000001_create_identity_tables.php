<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name', 120);
            $table->string('email', 190)->unique();
            $table->string('phone', 32)->nullable()->unique();
            $table->timestampTz('email_verified_at')->nullable();
            $table->string('password');
            $table->string('status', 24)->default('active')->index();
            $table->rememberToken();
            $table->timestampsTz();
        });
        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary(); $table->string('token'); $table->timestamp('created_at')->nullable();
        });
        Schema::create('roles', function (Blueprint $table): void { $table->id(); $table->string('name', 50)->unique(); $table->string('label', 80); $table->timestampsTz(); });
        Schema::create('role_user', function (Blueprint $table): void {
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id','role_id']);
        });
        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary(); $table->foreignUlid('user_id')->nullable()->index(); $table->string('ip_address', 45)->nullable(); $table->text('user_agent')->nullable(); $table->longText('payload'); $table->integer('last_activity')->index();
        });
    }
    public function down(): void { Schema::dropIfExists('sessions'); Schema::dropIfExists('role_user'); Schema::dropIfExists('roles'); Schema::dropIfExists('password_reset_tokens'); Schema::dropIfExists('users'); }
};
