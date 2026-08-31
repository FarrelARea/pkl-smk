<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('teacher_student_assignments', 'school_id')) {
            Schema::table('teacher_student_assignments', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('student_id')->constrained('schools')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('teacher_company_assignments', 'school_id')) {
            Schema::table('teacher_company_assignments', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('company_id')->constrained('schools')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('teacher_student_assignments', 'school_id')) {
            Schema::table('teacher_student_assignments', function (Blueprint $table) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            });
        }

        if (Schema::hasColumn('teacher_company_assignments', 'school_id')) {
            Schema::table('teacher_company_assignments', function (Blueprint $table) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            });
        }
    }
};
