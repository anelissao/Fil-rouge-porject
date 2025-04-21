<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('restrict');
            $table->text('overall_comment')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index('submission_id');
            $table->index('evaluator_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
}; 