<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('operators', function (Blueprint $table): void {
            $table->string('short_code', 12)->nullable()->after('slug');
            $table->string('tagline', 160)->nullable()->after('email');
            $table->string('brand_color', 16)->nullable()->after('tagline');
            $table->decimal('rating', 3, 2)->default(4.00)->after('brand_color');
            $table->unsignedInteger('review_count')->default(0)->after('rating');
            $table->decimal('punctuality_percent', 5, 2)->default(90.00)->after('review_count');
        });
        Schema::table('buses', function (Blueprint $table): void {
            $table->string('comfort_class', 32)->default('standard')->after('bus_type');
            $table->jsonb('amenities')->nullable()->after('comfort_class');
        });
    }

    public function down(): void {
        Schema::table('buses', function (Blueprint $table): void {
            $table->dropColumn(['comfort_class', 'amenities']);
        });
        Schema::table('operators', function (Blueprint $table): void {
            $table->dropColumn(['short_code', 'tagline', 'brand_color', 'rating', 'review_count', 'punctuality_percent']);
        });
    }
};
