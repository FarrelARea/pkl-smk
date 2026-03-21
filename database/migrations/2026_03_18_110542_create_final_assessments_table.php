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
        Schema::create('final_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('internship_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_admin_id')->constrained('users')->onDelete('cascade');
            $table->integer('final_score');
            $table->text('comments')->nullable();
            $table->enum('grade', ['A', 'B', 'C', 'D', 'E', 'F']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_assessments');
    }
};
