<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('internship_id')->constrained('internships')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('student_documents')->onDelete('set null');
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type'); // pdf, doc, docx
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('teacher_note')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
