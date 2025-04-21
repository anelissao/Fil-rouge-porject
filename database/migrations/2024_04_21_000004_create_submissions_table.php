<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brief_id')->constrained()->onDelete('restrict');
            $table->foreignId('student_id')->constrained('users')->onDelete('restrict');
            $table->text('content');
            $table->string('file_path')->nullable();
            $table->dateTime('submission_date');
            $table->enum('status', ['draft', 'submitted', 'evaluated'])->default('draft');
            $table->timestamps();

            $table->unique(['brief_id', 'student_id']);
            $table->index('status');
        });

        // Note: PostgreSQL doesn't support subqueries in check constraints
        // We'll handle this validation at the application level in the Submission model
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
}; 