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
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedInteger('distance_threshold')->default(100)->after('longitude');
            $table->string('province_name')->nullable()->after('address');
            $table->string('city_name')->nullable()->after('province_name');
            $table->string('district_name')->nullable()->after('city_name');
            $table->string('village_name')->nullable()->after('district_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['distance_threshold', 'province_name', 'city_name', 'district_name', 'village_name']);
        });
    }
};
