<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\CourseUnit;

#[Layout('components.layouts.app.sidebar')]
class TeacherOverview extends Component
{
    public $selectedGroupId = null;
    public $searchStudent = '';
    public $sortBy = 'name';
    public $filterStar = null; // null = all, 'gold', 'silver', 'bronze', 'failed'
    public $showStats = false; // Toggle dla statystyk klasy
    public $viewMode = 'all'; // 'all' = wyniki całej grupy, 'corrections' = poprawki
    public $selectedStudentId = null; // ID wybranego ucznia do szczegółowego widoku

    public function mount()
    {
        // Wychowawca może widzieć osiągnięcia swoich uczniów
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || (!$user->hasRole('wychowawca') && !$user->hasRole('koordynator') && !$user->hasRole('admin'))) {
            abort(403, 'Brak dostępu do tego panelu');
        }
    }

    public function selectGroup($groupId)
    {
        $this->selectedGroupId = $groupId;
        $this->searchStudent = '';
    }

    public function clearSearch()
    {
        $this->searchStudent = '';
    }

    public function setSortBy($sort)
    {
        $this->sortBy = $sort;
    }

    public function setFilterStar($starType)
    {
        $this->filterStar = $this->filterStar === $starType ? null : $starType;
    }

    public function toggleStats()
    {
        $this->showStats = !$this->showStats;
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->selectedStudentId = null;
    }

    public function selectStudent($studentId)
    {
        $this->selectedStudentId = $studentId;
    }

    public function backToList()
    {
        $this->selectedStudentId = null;
    }

    public function assignStar($studentId, $topicId, $starType)
    {
        try {
            $student = User::find($studentId);

            if (!$student) {
                $this->dispatch('star-error', ['message' => 'Nie znaleziono ucznia']);
                return;
            }

            UserAchievement::updateOrCreate(
                [
                    'user_id' => $studentId,
                    'course_unit_id' => $topicId,
                ],
                [
                    'star_type' => $starType,
                    'assigned_by_id' => Auth::id(),
                    'assigned_at' => now(),
                ]
            );

            // Wyślij event z typem gwiazdki dla SweetAlert
            $this->dispatch('star-assigned', ['starType' => $starType]);
        } catch (\Exception $e) {
            $this->dispatch('star-error', ['message' => 'Wystąpił błąd podczas przydzielania gwiazdki']);
        }
    }

    public function removeStar($studentId, $topicId)
    {
        try {
            UserAchievement::where('user_id', $studentId)
                ->where('course_unit_id', $topicId)
                ->delete();

            $this->dispatch('star-removed');
        } catch (\Exception $e) {
            $this->dispatch('star-error', ['message' => 'Wystąpił błąd podczas usuwania gwiazdki']);
        }
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Domyślne, aby uniknąć niezainicjalizowanych zmiennych w scenariuszach bez grupy
        $achievements = collect();
        $students = collect();

        // Pobierz grupy
        if ($user->hasRole('admin') || $user->hasRole('koordynator')) {
            $groups = Group::where('active', true)
                ->whereHas('users', function ($q) {
                    $q->where('active', true)->role('student');
                })
                ->orderBy('name')
                ->get();
        } else {
            // Wychowawca widzi tylko swoją grupę (jeśli ma przypisaną, aktywną i z aktywnymi uczniami o roli student)
            if ($user->group && $user->group->active && $user->group->users()->where('active', true)->role('student')->exists()) {
                $groups = collect([$user->group]);
            } else {
                $groups = collect();
            }
            if ($groups->isEmpty()) {
                return view('livewire.teacher-overview', [
                    'groups' => collect(),
                    'students' => collect(),
                    'achievements' => collect(),
                    'classStats' => [],
                    'topicsWithoutAchievements' => collect(),
                ]);
            }
            // Automatycznie wybieram grupę wychowawcy
            if (!$this->selectedGroupId && $groups->isNotEmpty()) {
                $this->selectedGroupId = $groups->first()->id;
            }
        }

        // Pobierz osiągnięcia
        $achievementsQuery = UserAchievement::with(['user', 'courseUnit']);

        // Jeśli grupa jest wybrana, filtruj
        if ($this->selectedGroupId) {
            $selectedGroup = Group::find($this->selectedGroupId);
            if (!$selectedGroup) {
                $students = collect();
                $achievements = collect();
            } else {
                $studentsInGroup = $selectedGroup->users()->where('active', true)->role('student')->get()->pluck('id');
                $achievementsQuery->whereIn('user_id', $studentsInGroup);

                // Filtrowanie po typie gwiazdy
                if ($this->filterStar) {
                    $achievementsQuery->where('star_type', $this->filterStar);
                }

                $achievements = $achievementsQuery->orderBy('created_at', 'desc')->get();

                // Pobierz unikalnych studentów z osiągnięciami
                $studentIds = $achievements->pluck('user_id')->unique();

                // Pobierz również studentów bez osiągnięć
                $studentsQuery = $selectedGroup->users()
                    ->where('active', true)
                    ->role('student');

                // Wyszukiwanie po imieniu/nazwisku
                if ($this->searchStudent) {
                    $studentsQuery->where('name', 'like', '%' . $this->searchStudent . '%');
                }

                $students = $studentsQuery->get();

                // Sortuj
                $achievementsCollection = $achievements; // Capture for closure
                $students = $students->sort(function ($a, $b) use ($achievementsCollection) {
                    switch ($this->sortBy) {
                        case 'progress':
                            return $this->getStudentProgress($b, $achievementsCollection) - $this->getStudentProgress($a, $achievementsCollection);
                        case 'name_desc':
                            return strcmp($b->name, $a->name);
                        default: // 'name'
                            return strcmp($a->name, $b->name);
                    }
                });
            }
        } else {
            $students = collect();
            $achievements = collect();
        }

        // Oblicz statystyki klasy
        $classStats = $this->calculateClassStats($achievements, $students, $this->selectedGroupId);

        // Pobierz zagadnienia bez osiągnięć dla wybranej grupy
        $topicsWithoutAchievements = $this->getTopicsWithoutAchievements($this->selectedGroupId);

        // Pobierz dane o poprawkach (uczniowie z failed/bronze w każdym zagadnieniu)
        $correctionsData = $this->getCorrectionsData($this->selectedGroupId, $this->searchStudent);

        // Jeśli wybrany student, pobierz jego pełny indeks
        $studentDetail = null;
        if ($this->selectedStudentId) {
            $studentDetail = $this->getStudentDetail($this->selectedStudentId);
        }

        return view('livewire.teacher-overview', [
            'groups' => $groups,
            'students' => $students,
            'achievements' => $achievements,
            'classStats' => $classStats,
            'topicsWithoutAchievements' => $topicsWithoutAchievements,
            'correctionsData' => $correctionsData,
            'studentDetail' => $studentDetail,
        ]);
    }

    private function getStudentProgress($student, $achievements)
    {
        $studentAchievements = $achievements->where('user_id', $student->id);
        return $studentAchievements->count();
    }

    private function calculateClassStats($achievements, $students, $groupId)
    {
        if (!$groupId || $students->isEmpty()) {
            return [
                'totalStudents' => 0,
                'totalAchievements' => 0,
                'avgAchievementsPerStudent' => 0,
                'goldsCount' => 0,
                'silversCount' => 0,
                'bronzesCount' => 0,
                'failureCount' => 0,
                'achievementRate' => 0, // procent studentów z co najmniej 1 osiągnięciem
            ];
        }

        $totalStudents = $students->count();
        $totalAchievements = $achievements->count();
        $studentsWithAchievements = $achievements->groupBy('user_id')->count();

        return [
            'totalStudents' => $totalStudents,
            'totalAchievements' => $totalAchievements,
            'avgAchievementsPerStudent' => $totalStudents > 0 ? round($totalAchievements / $totalStudents, 1) : 0,
            'goldsCount' => $achievements->where('star_type', 'gold')->count(),
            'silversCount' => $achievements->where('star_type', 'silver')->count(),
            'bronzesCount' => $achievements->where('star_type', 'bronze')->count(),
            'failureCount' => $achievements->where('star_type', 'failed')->count(),
            'achievementRate' => $totalStudents > 0 ? round(($studentsWithAchievements / $totalStudents) * 100) : 0,
        ];
    }

    private function getCorrectionsData($groupId, $searchStudent = '')
    {
        if (!$groupId) {
            return collect();
        }

        $group = Group::find($groupId);
        if (!$group) {
            return collect();
        }

        $studentsQuery = $group->users()->where('active', true)->role('student');

        // Zastosuj filtr wyszukiwania
        if ($searchStudent) {
            $studentsQuery->where('name', 'like', '%' . $searchStudent . '%');
        }

        $studentIds = $studentsQuery->pluck('id');

        // Pobierz wszystkie bloki z zagadnieniami
        $blocks = CourseUnit::where('parent_id', null)
            ->where('is_required', true)
            ->with(['children' => function($q) {
                $q->where('is_required', true)->orderBy('position');
            }])
            ->orderBy('position')
            ->get();

        $corrections = [];

        foreach ($blocks as $block) {
            $blockData = [
                'block' => $block,
                'topics' => []
            ];

            foreach ($block->children as $topic) {
                // Znajdź uczniów z failed lub bronze w tym zagadnieniu
                $studentsNeedingCorrection = UserAchievement::where('course_unit_id', $topic->id)
                    ->whereIn('user_id', $studentIds)
                    ->whereIn('star_type', ['failed', 'bronze'])
                    ->with('user')
                    ->get();

                if ($studentsNeedingCorrection->isNotEmpty()) {
                    $blockData['topics'][] = [
                        'topic' => $topic,
                        'students' => $studentsNeedingCorrection
                    ];
                }
            }

            if (!empty($blockData['topics'])) {
                $corrections[] = $blockData;
            }
        }

        return collect($corrections);
    }

    private function getStudentDetail($studentId)
    {
        $student = User::find($studentId);
        if (!$student) {
            return null;
        }

        // Pobierz wszystkie bloki z zagadnieniami i osiągnięciami ucznia
        $blocks = CourseUnit::where('parent_id', null)
            ->where('is_required', true)
            ->with(['children' => function($q) use ($studentId) {
                $q->where('is_required', true)
                    ->with(['achievements' => function($aq) use ($studentId) {
                        $aq->where('user_id', $studentId);
                    }])
                    ->orderBy('position');
            }])
            ->orderBy('position')
            ->get();

        return [
            'student' => $student,
            'blocks' => $blocks
        ];
    }

    private function getTopicsWithoutAchievements($groupId)
    {
        if (!$groupId) {
            return collect();
        }

        // Pobierz wszystkich studentów w grupie
        $group = Group::find($groupId);
        if (!$group) {
            return collect();
        }

        $studentIds = $group->users()->where('active', true)->role('student')->pluck('id');

        // Pobierz wszystkie zagadnienia (topics - parent_id != null)
        $allTopics = CourseUnit::where('parent_id', '!=', null)
            ->where('is_required', true)
            ->orderBy('position')
            ->get();

        // Dla każdego zagadnienia, sprawdź ile studentów ma osiągnięcie
        $topicsStats = $allTopics->map(function ($topic) use ($studentIds) {
            $achievementsCount = UserAchievement::where('course_unit_id', $topic->id)
                ->whereIn('user_id', $studentIds)
                ->count();

            return [
                'topic' => $topic,
                'studentsWithAchievement' => $achievementsCount,
                'percentageCompleted' => count($studentIds) > 0 ? round(($achievementsCount / count($studentIds)) * 100) : 0,
            ];
        });

        // Zwróć zagadnienia, które mają mniej niż 50% studentów z osiągnięciami
        return $topicsStats->filter(function ($stat) {
            return $stat['percentageCompleted'] < 50;
        })->sortBy('percentageCompleted');
    }
}
