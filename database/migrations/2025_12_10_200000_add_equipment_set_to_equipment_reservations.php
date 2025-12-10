<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_reservations', function (Blueprint $table) {
            $table->foreignId('equipment_set_id')
                ->nullable()
                ->after('equipment_id')
                ->constrained('equipment_sets')
                ->nullOnDelete();

            $table->foreignId('equipment_id')->nullable()->change();
            $table->index('equipment_set_id');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_reservations', function (Blueprint $table) {
            $table->dropForeign(['equipment_set_id']);
            $table->dropColumn('equipment_set_id');
            $table->foreignId('equipment_id')->nullable(false)->change();
        });
    }
};
