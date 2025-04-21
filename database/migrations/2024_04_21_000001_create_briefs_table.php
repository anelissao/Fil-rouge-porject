<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('briefs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('restrict');
            $table->dateTime('deadline');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();

            $table->index('teacher_id');
            $table->index('status');
        });

        // Note: We'll handle deadline validation at the application level instead
    }

    public function down(): void
    {
        Schema::dropIfExists('briefs');
    }
}; 