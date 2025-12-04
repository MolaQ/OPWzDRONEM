<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'active', 'is_template', 'template_id',
        'default_flight_hours_required', 'default_sim_hours_required', 'require_lab',
        'calculated_theory_minutes', 'calculated_practice_flight_minutes',
        'calculated_practice_lab_minutes', 'calculated_simulator_minutes'
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_template' => 'boolean',
        'require_lab' => 'boolean',
        'calculated_theory_minutes' => 'integer',
        'calculated_practice_flight_minutes' => 'integer',
        'calculated_practice_lab_minutes' => 'integer',
        'calculated_simulator_minutes' => 'integer',
    ];

    public function units()
    {
        return $this->hasMany(CourseUnit::class);
    }

    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class);
    }

    public function template()
    {
        return $this->belongsTo(Course::class, 'template_id');
    }

    public function instances()
    {
        return $this->hasMany(Course::class, 'template_id');
    }

    /**
     * Przelicz godziny z jednostek kursu
     */
    public function calculateHours(): void
    {
        $units = $this->units()->whereNull('parent_id')->with('children')->get();

        $minutes = [
            'theory' => 0,
            'practice_flight' => 0,
            'practice_lab' => 0,
            'simulator' => 0,
        ];

        foreach ($units as $block) {
            // Sumuj czas zagadnień w bloku
            foreach ($block->children as $topic) {
                if ($topic->duration_minutes && isset($minutes[$topic->type])) {
                    $minutes[$topic->type] += (int)$topic->duration_minutes;
                }
            }
        }

        $this->update([
            'calculated_theory_minutes' => $minutes['theory'],
            'calculated_practice_flight_minutes' => $minutes['practice_flight'],
            'calculated_practice_lab_minutes' => $minutes['practice_lab'],
            'calculated_simulator_minutes' => $minutes['simulator'],
        ]);
    }

    /**
     * Skopiuj strukturę jednostek z szablonu
     */
    public function copyUnitsFromTemplate(Course $template): void
    {
        $blocks = $template->units()->whereNull('parent_id')->orderBy('position')->get();

        foreach ($blocks as $templateBlock) {
            $newBlock = $this->units()->create([
                'type' => $templateBlock->type,
                'title' => $templateBlock->title,
                'description' => $templateBlock->description,
                'is_required' => $templateBlock->is_required,
                'duration_minutes' => $templateBlock->duration_minutes,
                'position' => $templateBlock->position,
                'parent_id' => null,
            ]);

            // Kopiuj zagadnienia
            $topics = $template->units()->where('parent_id', $templateBlock->id)->orderBy('position')->get();
            foreach ($topics as $templateTopic) {
                $this->units()->create([
                    'type' => $templateTopic->type,
                    'title' => $templateTopic->title,
                    'description' => $templateTopic->description,
                    'is_required' => $templateTopic->is_required,
                    'duration_minutes' => $templateTopic->duration_minutes,
                    'position' => $templateTopic->position,
                    'parent_id' => $newBlock->id,
                ]);
            }
        }

        $this->calculateHours();
    }
}
