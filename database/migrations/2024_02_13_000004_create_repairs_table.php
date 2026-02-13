<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->text('issue_description');
            $table->string('service_center')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->enum('status', ['pending_courier', 'in_service', 'returning', 'completed'])->default('pending_courier');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
