<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            // Change enum to include Polish status names
            $table->enum('status', [
                'dostepny',      // available
                'wypozyczony',   // borrowed
                'w_uzyciu',      // in use (internal)
                'konserwacja',   // maintenance
                'uszkodzony'     // damaged
            ])->default('dostepny')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->enum('status', ['available', 'in_use', 'maintenance', 'damaged'])->default('available')->change();
        });
    }
};
