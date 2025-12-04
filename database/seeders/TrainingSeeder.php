<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseUnit;

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main course
        $course = Course::create([
            'name' => 'OPW z Dronem',
            'description' => 'Program szkoleniowy pilotów bezzałogowych statków latających (BPSL) - Operacyjna Procedura Warsztatowa z Dronem',
            'active' => true,
        ]);

        // BLOCK 1: TEORIA - Propozycja kursów A1/A3
        $teoriaBlock = CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => null,
            'title' => 'Teoria — Propozycja A1/A3',
            'description' => 'Część teoretyczna przygotowująca do egzaminu EASA - kategorii A1 i A3',
            'type' => 'theory',
            'is_required' => true,
            'position' => 0,
        ]);

        // Add theory topics (examples)
        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $teoriaBlock->id,
            'title' => 'Prawo lotnicze i regulacje EASA',
            'description' => 'Zapoznanie z przepisami lotniczymi i wymogami operacyjnymi',
            'type' => 'theory',
            'is_required' => true,
            'duration_minutes' => 180,
            'position' => 0,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $teoriaBlock->id,
            'title' => 'Bezpieczeństwo operacji',
            'description' => 'Zasady bezpiecznego prowadzenia operacji dronem',
            'type' => 'theory',
            'is_required' => true,
            'duration_minutes' => 120,
            'position' => 1,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $teoriaBlock->id,
            'title' => 'Meteorologia i warunki atmosferyczne',
            'description' => 'Wpływ warunków pogodowych na operacje dronem',
            'type' => 'theory',
            'is_required' => false,
            'duration_minutes' => 90,
            'position' => 2,
        ]);

        // BLOCK 2: LABORATORIA - Budowa i nawiązanie
        $labBlock = CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => null,
            'title' => 'Laboratoria — Budowa i nawiązanie',
            'description' => 'Części praktyczne w laboratoriach - montaż, kalibracja, diagnostyka',
            'type' => 'practice_lab',
            'is_required' => true,
            'position' => 1,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $labBlock->id,
            'title' => 'Budowa drona i komponenty',
            'description' => 'Poznanie struktury drona, silniki, ESC, kontrolery lotu',
            'type' => 'practice_lab',
            'is_required' => true,
            'duration_minutes' => 120,
            'position' => 0,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $labBlock->id,
            'title' => 'Kalibracja i przygotowanie sprzętu',
            'description' => 'Procedury kalibracji żyroskopu, akcelerometru i kompasu',
            'type' => 'practice_lab',
            'is_required' => true,
            'duration_minutes' => 90,
            'position' => 1,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $labBlock->id,
            'title' => 'Diagnostyka i troubleshooting',
            'description' => 'Rozwiązywanie problemów z dronami i sprzętem',
            'type' => 'practice_lab',
            'is_required' => false,
            'duration_minutes' => 60,
            'position' => 2,
        ]);

        // BLOCK 3: PRAKTYKA - Bezpieczeństwo Operacji (BSP)
        $praktykaBlock = CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => null,
            'title' => 'Praktyka — BSP (Bezpieczeństwo Operacji)',
            'description' => 'Praktyczne szkolenie lotów w terenie z uwzględnieniem bezpieczeństwa',
            'type' => 'practice_flight',
            'is_required' => true,
            'position' => 2,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $praktykaBlock->id,
            'title' => 'Pierwszy lot kontrolowany',
            'description' => 'Pierwszy kontrolowany lot w bezpiecznym środowisku',
            'type' => 'practice_flight',
            'is_required' => true,
            'duration_minutes' => 90,
            'position' => 0,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $praktykaBlock->id,
            'title' => 'Maneury podstawowe',
            'description' => 'Nauka podstawowych manewrów lotniczych - wznoszenie, opadanie, skręty',
            'type' => 'practice_flight',
            'is_required' => true,
            'duration_minutes' => 120,
            'position' => 1,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $praktykaBlock->id,
            'title' => 'Lot w złych warunkach',
            'description' => 'Szkolenie w trudnych warunkach atmosferycznych',
            'type' => 'practice_flight',
            'is_required' => false,
            'duration_minutes' => 90,
            'position' => 2,
        ]);

        // BLOCK 4: SYMULATOR - Trening
        $symulatorBlock = CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => null,
            'title' => 'Symulator — Trening',
            'description' => 'Szkolenie na symulatorach - bezpieczne i kontrolowane środowisko',
            'type' => 'simulator',
            'is_required' => true,
            'position' => 3,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $symulatorBlock->id,
            'title' => 'Obsługa symulatora',
            'description' => 'Zapoznanie się z interfejsem i sterowaniem w symulatorze',
            'type' => 'simulator',
            'is_required' => true,
            'duration_minutes' => 60,
            'position' => 0,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $symulatorBlock->id,
            'title' => 'Ćwiczenia z awarią silnika',
            'description' => 'Scenariusze awaryjne i reagowanie na usterki',
            'type' => 'simulator',
            'is_required' => true,
            'duration_minutes' => 120,
            'position' => 1,
        ]);

        CourseUnit::create([
            'course_id' => $course->id,
            'parent_id' => $symulatorBlock->id,
            'title' => 'Zaawansowane manewry',
            'description' => 'Zaawansowane techniki pilotażu na symulatorze',
            'type' => 'simulator',
            'is_required' => false,
            'duration_minutes' => 90,
            'position' => 2,
        ]);

        $this->command->info('   - Utworzono kurs "OPW z Dronem" ze strukturą 4 bloków');
        $this->command->info('   - Teoria (A1/A3), Laboratoria, Praktyka (BSP), Symulator');
        $this->command->info('   - Dodano przykładowe zagadnienia do każdego bloku');
    }
}

