<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Column already exists without FK (from a partial failed run), just add the FK
        if (Schema::hasColumn('attendance', 'attendance_point_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->foreignId('attendance_point_id')->nullable()->change();
                $table->foreign('attendance_point_id')->references('id')->on('attendance_points')->nullOnDelete();
            });
        } else {
            Schema::table('attendance', function (Blueprint $table) {
                $table->foreignId('attendance_point_id')->nullable()->after('location_distance')
                    ->constrained('attendance_points')->nullOnDelete();
            });
        }

        Schema::table('clock_in_outs', function (Blueprint $table) {
            $table->foreignId('attendance_point_id')->nullable()->after('is_within_range')
                ->constrained('attendance_points')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_point_id');
        });

        Schema::table('clock_in_outs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_point_id');
        });
    }
};
