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
        Schema::create('equipment_maintenance_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');

            // Typ naprawy/konserwacji
            $table->enum('type', [
                'preventive_maintenance',
                'repair',
                'inspection',
                'calibration',
                'battery_replacement',
                'cleaning',
                'software_update',
                'other'
            ])->default('other');

            // Szczegóły
            $table->text('description');
            $table->text('findings')->nullable(); // Co znaleziono podczas inspekcji
            $table->text('actions_taken')->nullable(); // Jakie działania podjęto

            // Koszty
            $table->decimal('cost', 10, 2)->nullable();

            // Kto wykonał
            $table->foreignId('performed_by_user_id')->constrained('users')->onDelete('cascade');

            // Daty
            $table->timestamp('performed_at');
            $table->timestamp('next_maintenance_recommended')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('equipment_id');
            $table->index('performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance_logs');
    }
};
