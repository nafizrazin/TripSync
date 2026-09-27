<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('bookings',function(Blueprint $table): void { $table->foreignUlid('seat_hold_id')->nullable()->after('trip_id')->unique()->constrained('seat_holds')->nullOnDelete(); }); } public function down(): void { Schema::table('bookings',fn(Blueprint $table)=>$table->dropConstrainedForeignId('seat_hold_id')); } };
