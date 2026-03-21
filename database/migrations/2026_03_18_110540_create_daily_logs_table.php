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
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('internship_id')->constrained()->onDelete('cascade');
            $table->date('log_date');
            $table->text('activities');
            $table->text('reflection')->nullable();
            $table->text('teacher_comment')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['student_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
