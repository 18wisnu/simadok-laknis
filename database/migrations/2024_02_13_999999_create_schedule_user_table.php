<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Also make ends_at nullable in schedules table as it's no longer required
        Schema::table('schedules', function (Blueprint $table) {
            $table->timestamp('ends_at')->nullable()->change();
            // Remove the old user_id column from schedules as we now use pivot
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_user');
        
        Schema::table('schedules', function (Blueprint $table) {
            $table->timestamp('ends_at')->nullable(false)->change();
            $table->foreignId('user_id')->nullable()->constrained();
        });
    }
};
