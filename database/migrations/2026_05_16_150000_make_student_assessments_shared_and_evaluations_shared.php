<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_assessments', function (Blueprint $table) {
            $table->foreignId('last_updated_by_id')->nullable()->after('teacher_id')->constrained('users')->nullOnDelete();
        });

        DB::statement('ALTER TABLE student_assessments MODIFY teacher_id BIGINT UNSIGNED NULL');

        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreignId('last_updated_by_id')->nullable()->after('evaluator_id')->constrained('users')->nullOnDelete();
        });

        $duplicates = DB::table('evaluations')
            ->select('student_id', 'internship_id', DB::raw('MAX(id) as keep_id'))
            ->groupBy('student_id', 'internship_id')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('evaluations')
                ->where('student_id', $duplicate->student_id)
                ->where('internship_id', $duplicate->internship_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('evaluations', function (Blueprint $table) {
            $table->unique(['student_id', 'internship_id']);
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'internship_id']);
            $table->dropConstrainedForeignId('last_updated_by_id');
        });

        DB::statement('ALTER TABLE student_assessments MODIFY teacher_id BIGINT UNSIGNED NOT NULL');

        Schema::table('student_assessments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('last_updated_by_id');
        });
    }
};
