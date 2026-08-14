<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('daily_logs', function (Blueprint $table) {
                $table->dropForeign('daily_logs_student_id_foreign');
            });
        }

        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropUnique('daily_logs_student_id_log_date_unique');
            $table->index('student_id', 'daily_logs_student_id_index');
        });

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('daily_logs', function (Blueprint $table) {
                $table->foreign('student_id', 'daily_logs_student_id_foreign')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('daily_logs', function (Blueprint $table) {
                $table->dropForeign('daily_logs_student_id_foreign');
            });
        }

        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropIndex('daily_logs_student_id_index');
            $table->unique(['student_id', 'log_date'], 'daily_logs_student_id_log_date_unique');
        });

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('daily_logs', function (Blueprint $table) {
                $table->foreign('student_id', 'daily_logs_student_id_foreign')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};
