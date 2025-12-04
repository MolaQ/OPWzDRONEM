<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Kurs może być szablonem (template) lub instancją
            $table->boolean('is_template')->default(false)->after('active');
            $table->foreignId('template_id')->nullable()->after('is_template')->constrained('courses')->nullOnDelete();
            
            // Przeliczone godziny z jednostek (zamiast ręcznego ustawiania)
            $table->unsignedInteger('calculated_theory_minutes')->default(0)->after('require_lab');
            $table->unsignedInteger('calculated_practice_flight_minutes')->default(0)->after('calculated_theory_minutes');
            $table->unsignedInteger('calculated_practice_lab_minutes')->default(0)->after('calculated_practice_flight_minutes');
            $table->unsignedInteger('calculated_simulator_minutes')->default(0)->after('calculated_practice_lab_minutes');
        });

        Schema::table('student_courses', function (Blueprint $table) {
            // Instancja kursu może być przypisana do grupy
            $table->foreignId('group_id')->nullable()->after('course_id')->constrained('groups')->nullOnDelete();
            $table->date('start_date')->nullable()->after('group_id');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('student_courses', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn(['group_id', 'start_date', 'end_date']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn([
                'is_template', 'template_id',
                'calculated_theory_minutes', 'calculated_practice_flight_minutes',
                'calculated_practice_lab_minutes', 'calculated_simulator_minutes'
            ]);
        });
    }
};
