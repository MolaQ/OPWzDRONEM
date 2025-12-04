<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_material_id')->constrained('course_unit_materials')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('comment');
            $table->timestamps();

            $table->index('course_material_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_comments');
    }
};
