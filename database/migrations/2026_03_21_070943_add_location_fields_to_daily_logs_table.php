<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('context');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->boolean('location_verified')->default(false)->after('longitude');
            $table->decimal('location_distance', 10, 2)->nullable()->after('location_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'location_verified', 'location_distance']);
        });
    }
};
