<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;

#[Layout('components.layouts.app.sidebar')]
class Awards extends Component
{
    use WithPagination;

    public $selectedGroupId = null;
    public $selectedBlockId = null;
    public $selectedTopicId = null;
    public $search = '';
    public $showModal = false;
    public $selectedStudent = null;
    public $starType = 'bronze';
    public $notes = '';

    protected $updatesQueryString = ['selectedGroupId', 'selectedBlockId', 'selectedTopicId', 'search', 'page'];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSelectedGroupId() {
        $this->selectedBlockId = null;
        $this->selectedTopicId = null;
        $this->resetPage();
    }
    public function updatingSelectedBlockId() {
        $this->selectedTopicId = null;
        $this->resetPage();
    }
    public function updatingSelectedTopicId() {
        $this->resetPage();
    }

    public function mount()
    {
        $this->authorize('achievements.view');
    }

    public function assignStar($studentId, $starType)
    {
        $this->authorize('achievements.assign');

        if (!$this->selectedTopicId) {
            return;
        }

        $student = User::find($studentId);

        UserAchievement::updateOrCreate(
            [
                'user_id' => $studentId,
                'course_unit_id' => $this->selectedTopicId,
            ],
            [
                'star_type' => $starType,
                'assigned_by_id' => Auth::id(),
                'assigned_at' => now(),
            ]
        );

        // Dispatchuj event z informacją o przydzielonej gwieździe
        $starLabels = [
            'gold' => 'Złoto ⭐ (90-100%)',
            'silver' => 'Srebro ⭐ (70-89%)',
            'bronze' => 'Brąz ⭐ (50-69%)',
            'failed' => 'Szary ⭐ (<50%)',
        ];

        $this->dispatch('star-awarded', studentName: $student->name, starLabel: $starLabels[$starType] ?? 'Nieznana');
    }

    public function removeStar($studentId)
    {
        $this->authorize('achievements.remove');

        if (!$this->selectedTopicId) {
            return;
        }

        UserAchievement::where('user_id', $studentId)
            ->where('course_unit_id', $this->selectedTopicId)
            ->delete();

        $this->dispatch('award-removed');
    }

    public function selectGroup($groupId)
    {
        $this->selectedGroupId = $groupId;
        $this->selectedBlockId = null;
        $this->selectedTopicId = null;
    }

    public function selectBlock($blockId)
    {
        $this->selectedBlockId = $blockId;
        $this->selectedTopicId = null;
    }

    public function selectTopic($topicId)
    {
        $this->selectedTopicId = $topicId;
    }

    public function render()
    {
        $user = Auth::user();
        $userRoles = $user->roles->pluck('name');

        // Pobierz grupy z aktywnymi uczniami
        $groups = collect();
        if ($userRoles->contains('instructor') || $userRoles->contains('wychowawca')) {
            $groups = \App\Models\Group::where('id', $user->group_id)
                ->whereHas('users', function($q) {
                    $q->role('student')->where('active', true);
                })
                ->get();
            if (!$this->selectedGroupId && $groups->isNotEmpty()) {
                $this->selectedGroupId = $groups->first()->id;
            }
        } else {
            $groups = \App\Models\Group::orderBy('name')
                ->whereHas('users', function($q) {
                    $q->role('student')->where('active', true);
                })
                ->get();
        }

        // Pobierz wszystkie bloki (CourseUnit z parent_id = null)
        $blocks = collect();
        if ($this->selectedGroupId) {
            $blocks = CourseUnit::where('parent_id', null)
                ->where('is_required', true)
                ->orderBy('position')
                ->get();
        }

        // Pobierz wszystkie zagadnienia dla wybranego bloku
        $topics = collect();
        if ($this->selectedGroupId && $this->selectedBlockId) {
            $topics = CourseUnit::where('parent_id', $this->selectedBlockId)
                ->where('is_required', true)
                ->orderBy('position')
                ->get();
        }

        // Pobierz uczniów tylko jeśli wybrano grupę, blok i zagadnienie
        $students = collect();
        $selectedTopic = null;
        $achievements = collect();

        if ($this->selectedGroupId && $this->selectedBlockId && $this->selectedTopicId) {
            $selectedTopic = CourseUnit::find($this->selectedTopicId);

            $studentsQuery = User::role('student')
                ->where('active', true)
                ->where('group_id', $this->selectedGroupId);

            // Filtr wyszukiwania
            if ($this->search) {
                $studentsQuery->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            }

            $students = $studentsQuery->orderBy('name')->get();

            // Załaduj osiągnięcia dla wybranego zagadnienia
            $achievements = UserAchievement::where('course_unit_id', $this->selectedTopicId)
                ->whereIn('user_id', $students->pluck('id'))
                ->get()
                ->keyBy('user_id');
        }

        return view('livewire.admin.awards', [
            'groups' => $groups,
            'blocks' => $blocks,
            'topics' => $topics,
            'students' => $students,
            'selectedTopic' => $selectedTopic,
            'achievements' => $achievements,
        ]);
    }
}
