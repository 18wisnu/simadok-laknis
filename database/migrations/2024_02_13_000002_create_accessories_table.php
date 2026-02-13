<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_removable')->default(true); // Includes batteries/media
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
