<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->timestamp('borrowed_at');
            $table->timestamp('expected_return_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->enum('status', ['active', 'returned', 'late'])->default('active');
            $table->enum('condition_on_return', ['good', 'damaged'])->nullable();
            $table->text('accessories_brought_json')->nullable(); // Snapshot of accessories taken
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
