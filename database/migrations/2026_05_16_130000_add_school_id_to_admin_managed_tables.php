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
            ->join('companies', 'teacher_company_assignments.company_id', '=', 'companies.id')
            ->update(['teacher_company_assignments.school_id' => DB::raw('companies.school_id')]);

        DB::table('teacher_student_assignments')
            ->join('users as students', 'teacher_student_assignments.student_id', '=', 'students.id')
            ->update(['teacher_student_assignments.school_id' => DB::raw('students.school_id')]);

        DB::table('attendance_points')
            ->join('companies', 'attendance_points.company_id', '=', 'companies.id')
            ->update(['attendance_points.school_id' => DB::raw('companies.school_id')]);

        DB::table('document_requirements')
            ->leftJoin('classes', 'document_requirements.class_id', '=', 'classes.id')
            ->leftJoin('users as teachers', 'document_requirements.teacher_id', '=', 'teachers.id')
            ->update(['document_requirements.school_id' => DB::raw('COALESCE(classes.school_id, teachers.school_id)')]);

        DB::table('assessment_templates')
            ->join('classes', 'assessment_templates.class_id', '=', 'classes.id')
            ->update(['assessment_templates.school_id' => DB::raw('classes.school_id')]);
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
