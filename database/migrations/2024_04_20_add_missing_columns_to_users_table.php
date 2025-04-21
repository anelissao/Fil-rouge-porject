<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add username column if it doesn't exist
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique();
            }

            // Add first_name column if it doesn't exist
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name');
            }

            // Add last_name column if it doesn't exist
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name');
            }

            // Add role column if it doesn't exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student');
            }

            // Add google_id column if it doesn't exist
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable();
            }
        });

        // Check if constraint exists before adding it
        $constraintExists = DB::select("
            SELECT count(*) as count
            FROM pg_constraint 
            WHERE conname = 'users_role_check'
        ");

        if ($constraintExists[0]->count == 0) {
            // Add check constraint for role values
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'teacher', 'student'))");
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove columns if they exist
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropColumn('google_id');
            }
        });
    }
}; 