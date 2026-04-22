<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires explicitly altering the enum column.
        // SQLite uses varchar internally and doesn't enforce enum values.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('school_admin','teacher','student','company_supervisor','superadmin') DEFAULT 'student'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('school_admin','teacher','student','company_supervisor') DEFAULT 'student'");
        }
    }
};
