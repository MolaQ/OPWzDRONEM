<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class StudentDashboard extends Component
{
    public $courseId = 1; // Default to 'OPW z Dronem'

    public function mount()
    {
        // Ensure user is a student
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('student')) {
            abort(403, 'Tylko uczniowie mają dostęp do tego panelu.');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $course = Course::find($this->courseId);

        // Pobierz wszystkie bloki (CourseUnit z parent_id = null)
        $blocks = collect();
        if ($course) {
            $blocks = CourseUnit::where('parent_id', null)
                ->where('is_required', true)
                ->orderBy('position')
                ->get();
        }

        // Pobierz osiągnięcia ucznia
        $achievements = collect();
        $blockProgress = collect();

        if ($course) {
            // Pobierz wszystkie osiągnięcia ucznia dla tego kursu
            $achievements = UserAchievement::whereIn('course_unit_id', $course->units->pluck('id'))
                ->where('user_id', $user->id)
                ->with('courseUnit')
                ->get();

            // Oblicz postęp dla każdego bloku
            foreach ($blocks as $block) {
                $blockTopics = $block->children()->get();
                $topicsWithAchievements = 0;

                foreach ($blockTopics as $topic) {
                    $achievement = $achievements->firstWhere('course_unit_id', $topic->id);
                    if ($achievement) {
                        $topicsWithAchievements++;
                    }
                }

                $blockProgress[$block->id] = [
                    'total' => $blockTopics->count(),
                    'completed' => $topicsWithAchievements,
                    'percentage' => $blockTopics->count() > 0 ? round(($topicsWithAchievements / $blockTopics->count()) * 100) : 0,
                ];
            }
        }

        return view('livewire.student-dashboard', [
            'course' => $course,
            'blocks' => $blocks,
            'achievements' => $achievements,
            'blockProgress' => $blockProgress,
            'user' => $user,
        ]);
    }
}
