<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permission_requests', function (Blueprint $table): void {
            $table->enum('type', ['sick', 'permit', 'other', 'holiday'])->change();
        });

        Schema::table('attendance', function (Blueprint $table): void {
            $table->enum('status', ['present', 'absent', 'sick', 'permission', 'holiday'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('permission_requests', function (Blueprint $table): void {
            $table->enum('type', ['sick', 'permit', 'other'])->change();
        });

        Schema::table('attendance', function (Blueprint $table): void {
            $table->enum('status', ['present', 'absent', 'sick', 'permission'])->change();
        });
    }
};
