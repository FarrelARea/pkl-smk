<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_company_assignments', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('company_id')->constrained()->nullOnDelete();
        });

        Schema::table('teacher_student_assignments', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('student_id')->constrained()->nullOnDelete();
        });

        Schema::table('attendance_points', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('company_id')->constrained()->nullOnDelete();
        });

        Schema::table('document_requirements', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('teacher_id')->constrained()->nullOnDelete();
        });

        Schema::table('assessment_templates', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('class_id')->constrained()->nullOnDelete();
        });

        DB::table('teacher_company_assignments')
            ->update([
                'school_id' => DB::raw('(SELECT companies.school_id FROM companies WHERE companies.id = teacher_company_assignments.company_id)'),
            ]);

        DB::table('teacher_student_assignments')
            ->update([
                'school_id' => DB::raw('(SELECT students.school_id FROM users AS students WHERE students.id = teacher_student_assignments.student_id)'),
            ]);

        DB::table('attendance_points')
            ->update([
                'school_id' => DB::raw('(SELECT companies.school_id FROM companies WHERE companies.id = attendance_points.company_id)'),
            ]);

        DB::table('document_requirements')
            ->update([
                'school_id' => DB::raw('COALESCE((SELECT classes.school_id FROM classes WHERE classes.id = document_requirements.class_id), (SELECT teachers.school_id FROM users AS teachers WHERE teachers.id = document_requirements.teacher_id))'),
            ]);

        DB::table('assessment_templates')
            ->update([
                'school_id' => DB::raw('(SELECT classes.school_id FROM classes WHERE classes.id = assessment_templates.class_id)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('assessment_templates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });

        Schema::table('document_requirements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });

        Schema::table('attendance_points', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });

        Schema::table('teacher_student_assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });

        Schema::table('teacher_company_assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });
    }
};
