<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if the name column exists and make it nullable
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert name column back to not null if it exists
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable(false)->change();
            }
        });
    }
}; 