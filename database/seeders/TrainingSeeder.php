<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\User;
use App\Models\PilotLicense;
use App\Services\CourseService;

class TrainingSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::create([
            'name' => 'Kurs operatora drona (OPW)',
            'description' => 'Program kursu obejmujący teorię, praktykę, laboratorium i symulator.',
            'active' => true,
            'is_template' => true, // To jest szablon
            'default_flight_hours_required' => 4,
            'default_sim_hours_required' => 6,
            'require_lab' => true,
        ]);

        // Create high-level blocks (parentless units)
        $blocks = [
            ['type' => 'theory', 'title' => 'Teoria — podstawy', 'description' => null, 'is_required' => true, 'position' => 1],
            ['type' => 'practice_lab', 'title' => 'Laboratorium — budowa', 'description' => null, 'is_required' => true, 'position' => 2],
            ['type' => 'simulator', 'title' => 'Symulator — trening', 'description' => null, 'is_required' => true, 'position' => 3],
            ['type' => 'practice_flight', 'title' => 'Praktyka — loty', 'description' => null, 'is_required' => true, 'position' => 4],
        ];

        $createdBlocks = [];
        foreach ($blocks as $b) {
            $createdBlocks[$b['type']] = CourseUnit::create(array_merge($b, ['course_id' => $course->id]));
        }

        // Topics under blocks
        $topicsByBlock = [
            'theory' => [
                ['title' => 'Przepisy lotnicze', 'description' => 'Podstawy prawa lotniczego.', 'is_required' => true, 'position' => 1],
                ['title' => 'Bezpieczeństwo operacji', 'description' => 'Zasady bezpiecznego latania.', 'is_required' => true, 'position' => 2],
            ],
            'practice_lab' => [
                ['title' => 'Składanie drona', 'description' => 'Montaż ramy i podzespołów.', 'is_required' => true, 'position' => 1],
                ['title' => 'Lutowanie podzespołów', 'description' => 'Lutowanie ESC, FC, silników.', 'is_required' => true, 'position' => 2],
                ['title' => 'Konfiguracja oprogramowania', 'description' => 'Konfiguracja Betaflight/INAV.', 'is_required' => true, 'position' => 3],
            ],
            'simulator' => [
                ['title' => 'Ćwiczenia na symulatorze', 'description' => 'Loty treningowe.', 'is_required' => true, 'position' => 1, 'duration_minutes' => 120],
            ],
            'practice_flight' => [
                ['title' => 'Loty podstawowe', 'description' => 'Start, zawis, lądowanie.', 'is_required' => true, 'position' => 1, 'duration_minutes' => 60],
            ],
        ];

        $createdUnits = [];
        foreach ($topicsByBlock as $type => $topics) {
            $block = $createdBlocks[$type];
            foreach ($topics as $t) {
                $createdUnits[] = CourseUnit::create(array_merge(
                    ['course_id' => $course->id, 'type' => $type, 'parent_id' => $block->id],
                    $t
                ));
            }
        }

        $service = new CourseService();
        $students = User::where('role', 'student')->limit(10)->get();
        foreach ($students as $student) {
            // Enroll student with course defaults
            $studentCourse = $service->enrollStudent($student, $course);
            // Assign required theory units
            foreach ($createdUnits as $unit) {
                if ($unit->is_required) {
                    // Assign only theory and lab initially; flight/simulator later
                    if (in_array($unit->type, ['theory','practice_lab'])) {
                        $service->assignUnit($studentCourse, $unit, assignedByUserId: $student->id);
                    }
                }
            }
        }

        // Przelicz godziny z jednostek
        $course->calculateHours();

        $this->command->info('   - Utworzono kurs, bloki i zagadnienia szkoleniowe');
        $this->command->info('   - Zapisano 10 studentów i przypisano zagadnienia teoretyczne/laboratoryjne');
        $this->command->info('   - Przeliczono godziny z jednostek kursu');
    }
}
