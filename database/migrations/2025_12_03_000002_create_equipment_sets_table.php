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
        Schema::create('equipment_sets', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique(); // Z0000000001
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('equipment_set_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_set_id')->constrained('equipment_sets')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate equipment in same set
            $table->unique(['equipment_set_id', 'equipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_set_items');
        Schema::dropIfExists('equipment_sets');
    }
};
