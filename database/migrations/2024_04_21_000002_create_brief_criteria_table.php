<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brief_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brief_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order');
            $table->timestamps();

            $table->index('brief_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brief_criteria');
    }
}; 