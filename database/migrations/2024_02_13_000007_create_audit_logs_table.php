<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $row) {
            $row->id();
            $row->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $row->string('action'); // created, updated, deleted
            $row->string('model_type');
            $row->unsignedBigInteger('model_id');
            $row->json('old_values')->nullable();
            $row->json('new_values')->nullable();
            $row->text('description')->nullable();
            $row->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
