<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained('brief_tasks')->onDelete('cascade');
            $table->boolean('response');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['evaluation_id', 'task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_answers');
    }
}; 