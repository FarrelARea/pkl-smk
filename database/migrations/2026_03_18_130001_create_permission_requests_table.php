<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('internship_id');
            $table->date('request_date');
            $table->enum('type', ['sick', 'permit', 'other']);
            $table->text('reason')->nullable();
            $table->string('document')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->text('handler_note')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('internship_id')->references('id')->on('internships')->onDelete('cascade');
            $table->foreign('handled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_requests');
    }
};
