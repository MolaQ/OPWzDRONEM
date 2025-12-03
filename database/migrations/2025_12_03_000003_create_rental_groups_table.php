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
        Schema::create('rental_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Auto-generated or custom
            $table->timestamps();
        });

        Schema::create('rental_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_group_id')->constrained('rental_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate members
            $table->unique(['rental_group_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_group_members');
        Schema::dropIfExists('rental_groups');
    }
};
