<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop FK first (it depends on the unique index), then drop unique, re-add FK with regular index
        DB::statement('ALTER TABLE daily_logs DROP FOREIGN KEY daily_logs_student_id_foreign');
        DB::statement('ALTER TABLE daily_logs DROP INDEX daily_logs_student_id_log_date_unique');
        DB::statement('ALTER TABLE daily_logs ADD INDEX daily_logs_student_id_index (student_id)');
        DB::statement('ALTER TABLE daily_logs ADD CONSTRAINT daily_logs_student_id_foreign FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE daily_logs DROP FOREIGN KEY daily_logs_student_id_foreign');
        DB::statement('ALTER TABLE daily_logs DROP INDEX daily_logs_student_id_index');
        DB::statement('ALTER TABLE daily_logs ADD UNIQUE daily_logs_student_id_log_date_unique (student_id, log_date)');
        DB::statement('ALTER TABLE daily_logs ADD CONSTRAINT daily_logs_student_id_foreign FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE');
    }
};
