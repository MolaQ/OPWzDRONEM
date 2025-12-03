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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();

            // Who rented
            $table->foreignId('rental_group_id')->nullable()->constrained('rental_groups')->onDelete('cascade');

            // What was rented
            $table->foreignId('equipment_id')->nullable()->constrained('equipments')->onDelete('set null');
            $table->foreignId('equipment_set_id')->nullable()->constrained('equipment_sets')->onDelete('set null');

            // When
            $table->timestamp('rented_at');
            $table->timestamp('returned_at')->nullable();

            // Who processed
            $table->foreignId('rented_by_user_id')->constrained('users')->onDelete('cascade'); // admin/instructor
            $table->foreignId('returned_by_user_id')->nullable()->constrained('users')->onDelete('set null');

            // Notes
            $table->text('rental_notes')->nullable(); // Notes when renting
            $table->text('return_notes')->nullable(); // Notes when returning (e.g., damage)

            $table->timestamps();

            // Indexes for performance
            $table->index('rented_at');
            $table->index('returned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
