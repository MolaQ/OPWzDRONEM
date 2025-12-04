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
        Schema::create('course_unit_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_unit_id')->constrained('course_units')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable(); // Opcjonalny opis materiału
            $table->enum('type', ['pdf', 'video_link', 'external_link'])->default('pdf');
            $table->text('url_or_file_path'); // ścieżka pliku lub URL
            $table->foreignId('uploaded_by_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_approved')->default(false); // musi być zaakceptowane przez admina/instruktora
            $table->text('rejection_reason')->nullable(); // powód odrzucenia
            $table->timestamps();

            $table->index('course_unit_id');
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_unit_materials');
    }
};
