<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('default_flight_hours_required')->default(0);
            $table->unsignedInteger('default_sim_hours_required')->default(0);
            $table->boolean('require_lab')->default(true);
            $table->timestamps();
        });

        Schema::create('course_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->enum('type', ['theory','practice_flight','practice_lab','simulator']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->unsignedInteger('flight_hours_required')->default(0);
            $table->unsignedInteger('sim_hours_required')->default(0);
            $table->boolean('require_lab')->default(true);
            $table->enum('status', ['active','completed','inactive'])->default('active');
            $table->timestamps();
            $table->unique(['user_id','course_id']);
        });

        Schema::create('student_unit_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_course_id')->constrained('student_courses')->cascadeOnDelete();
            $table->foreignId('course_unit_id')->constrained('course_units')->cascadeOnDelete();
            $table->enum('status', ['pending','assigned','completed'])->default('pending');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['student_course_id','course_unit_id']);
        });

        // No dedicated pilot licenses table; use User fields for validity
    }

    public function down(): void
    {
        Schema::dropIfExists('student_unit_progresses');
        Schema::dropIfExists('student_courses');
        Schema::dropIfExists('course_units');
        Schema::dropIfExists('courses');
    }
};
