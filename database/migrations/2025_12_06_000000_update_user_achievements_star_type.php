<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL specific: Modify the ENUM column to include 'failed'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE user_achievements MODIFY COLUMN star_type ENUM('failed', 'bronze', 'silver', 'gold') DEFAULT 'bronze'");
            return;
        }

        // For SQLite or other drivers used in tests, skip to avoid unsupported ALTER syntax
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Revert to the original ENUM without 'failed'
            DB::statement("ALTER TABLE user_achievements MODIFY COLUMN star_type ENUM('bronze', 'silver', 'gold') DEFAULT 'bronze'");
        }
    }
};
