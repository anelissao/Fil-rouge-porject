<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brief_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria_id')->constrained('brief_criteria')->onDelete('cascade');
            $table->text('description');
            $table->integer('order');
            $table->timestamps();

            $table->index('criteria_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brief_tasks');
    }
}; 