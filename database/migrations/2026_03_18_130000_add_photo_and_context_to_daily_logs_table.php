<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('reflection');
            $table->text('context')->nullable()->after('activities');
        });
    }

    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropColumn(['photo', 'context']);
        });
    }
};
