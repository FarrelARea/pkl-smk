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
        Schema::table('daily_log_comments', function (Blueprint $table) {
            $table->renameColumn('teacher_id', 'author_id');
            $table->enum('author_role', ['teacher', 'student'])->nullable()->after('author_id');
        });

        // Migrate existing rows: all existing comments were from teachers
        DB::table('daily_log_comments')->update(['author_role' => 'teacher']);

        Schema::table('daily_log_comments', function (Blueprint $table) {
            $table->enum('author_role', ['teacher', 'student'])->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('daily_log_comments', function (Blueprint $table) {
            $table->dropColumn('author_role');
            $table->renameColumn('author_id', 'teacher_id');
        });
    }
};
