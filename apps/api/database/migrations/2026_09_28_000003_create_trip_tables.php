<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('schedules', function (Blueprint $table): void { $table->ulid('id')->primary(); $table->foreignUlid('route_id')->constrained()->cascadeOnDelete(); $table->foreignUlid('bus_id')->constrained()->restrictOnDelete(); $table->time('departure_time'); $table->time('arrival_time'); $table->jsonb('days_of_week'); $table->decimal('base_fare',12,2); $table->date('effective_from'); $table->date('effective_until')->nullable(); $table->string('status',24)->default('active')->index(); $table->timestampsTz(); });
        Schema::create('trips', function (Blueprint $table): void { $table->ulid('id')->primary(); $table->string('public_id',32)->unique(); $table->foreignUlid('schedule_id')->nullable()->constrained()->nullOnDelete(); $table->foreignUlid('route_id')->constrained()->restrictOnDelete(); $table->foreignUlid('bus_id')->constrained()->restrictOnDelete(); $table->timestampTz('departure_at')->index(); $table->timestampTz('arrival_at'); $table->string('trip_status',24)->default('scheduled')->index(); $table->string('booking_status',24)->default('open')->index(); $table->decimal('base_fare',12,2); $table->timestampsTz(); $table->index(['route_id','departure_at']); });
    }
    public function down(): void { Schema::dropIfExists('trips'); Schema::dropIfExists('schedules'); }
};
