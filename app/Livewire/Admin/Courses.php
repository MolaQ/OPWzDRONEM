<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Group;
use App\Models\User;

#[Layout('components.layouts.app.sidebar')]
class Courses extends Component
{
    public string $name = '';
    public string $description = '';
    public int $default_flight_hours_required = 4;
    public int $default_sim_hours_required = 6;
    public bool $require_lab = true;
    public bool $is_template = false;

    public ?int $editingCourseId = null;

    // New instance creation
    public bool $showInstanceCreator = false;
    public ?int $selectedTemplateId = null;
    public ?int $selectedGroupId = null;
    public $manualStudentIds = [];

    // Filters
    public string $search = '';
    public ?string $filterCategory = null; // theory, practice_flight, practice_lab, simulator
    public bool $filterRequiredOnly = false;

    // Block/Topic editor state
    public ?int $editingUnitId = null;
    public ?int $editingParentId = null; // null for blocks, unit id for topics
    public string $unitTitle = '';
    public string $unitDescription = '';
    public string $unitType = 'theory';
    public bool $unitIsRequired = true;
    public ?int $unitDurationMinutes = null;
    public int $unitPosition = 0;

    public function saveCourse()
    {
        $data = $this->validate([
            'name' => 'required|string|min:3',
            'description' => 'nullable|string',
            'default_flight_hours_required' => 'required|integer|min:0',
            'default_sim_hours_required' => 'required|integer|min:0',
            'require_lab' => 'boolean',
            'is_template' => 'boolean',
        ]);

        if ($this->editingCourseId) {
            $course = Course::findOrFail($this->editingCourseId);
            $course->update(array_merge($data, ['active' => true]));
            session()->flash('success', 'Kurs zaktualizowany.');
        } else {
            $course = Course::create(array_merge($data, ['active' => true]));
            session()->flash('success', 'Szablon kursu utworzony.');
        }

        $this->resetForm();
    }

    public function editCourse(int $courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->editingCourseId = $course->id;
        $this->name = $course->name;
        $this->description = (string)($course->description ?? '');
        $this->default_flight_hours_required = (int)$course->default_flight_hours_required;
        $this->default_sim_hours_required = (int)$course->default_sim_hours_required;
        $this->require_lab = (bool)$course->require_lab;
        $this->is_template = (bool)$course->is_template;
    }

    public function resetForm()
    {
        $this->reset(['editingCourseId','name','description','default_flight_hours_required','default_sim_hours_required','require_lab','is_template']);
        $this->default_flight_hours_required = 4;
        $this->default_sim_hours_required = 6;
        $this->require_lab = true;
        $this->is_template = false;
    }

    public function deleteCourse(int $courseId)
    {
        $course = Course::findOrFail($courseId);
        $course->delete();
        session()->flash('success', 'Kurs usunięty.');
        $this->resetForm();
    }

    public function render()
    {
        $templates = Course::where('is_template', true)->withCount('units')->orderBy('name')->get();
        $instances = Course::where('is_template', false)->with('template')->withCount('units')->orderBy('created_at','desc')->get();

        // Load blocks and topics for the first template for structure editing
        $selectedCourse = $templates->first();
        $blocks = collect();
        if ($selectedCourse) {
            $query = CourseUnit::query()
                ->where('course_id', $selectedCourse->id)
                ->whereNull('parent_id');

            if ($this->filterCategory) {
                $query->where('type', $this->filterCategory);
            }
            if ($this->filterRequiredOnly) {
                $query->where('is_required', true);
            }
            if (trim($this->search) !== '') {
                $query->where(function($q){
                    $q->where('title','like','%'.trim($this->search).'%')
                      ->orWhere('description','like','%'.trim($this->search).'%');
                });
            }

            $blocks = $query->orderBy('position')->orderBy('id')->get();

            // Eager load children topics with filters applied per parent
            $blocks->load(['children' => function($childQ) {
                if ($this->filterCategory) {
                    $childQ->where('type', $this->filterCategory);
                }
                if ($this->filterRequiredOnly) {
                    $childQ->where('is_required', true);
                }
                if (trim($this->search) !== '') {
                    $search = trim($this->search);
                    $childQ->where(function($q) use ($search){
                        $q->where('title','like','%'.$search.'%')
                          ->orWhere('description','like','%'.$search.'%');
                    });
                }
                $childQ->orderBy('position')->orderBy('id');
            }]);
        }

        $groups = Group::where('active', true)
            ->withCount(['users' => function($q) {
                $q->where('active', true);
            }])
            ->orderBy('name')
            ->get();

        return view('livewire.admin.courses', [
            'templates' => $templates,
            'instances' => $instances,
            'selectedCourse' => $selectedCourse,
            'blocks' => $blocks,
            'groups' => $groups,
        ]);
    }

    public function startCreateBlock()
    {
        $this->editingUnitId = null;
        $this->editingParentId = null;
        $this->unitTitle = '';
        $this->unitDescription = '';
        $this->unitType = 'theory';
        $this->unitIsRequired = true;
        $this->unitDurationMinutes = null;
        $this->unitPosition = 0;
    }

    public function startCreateTopic(int $parentId)
    {
        $this->editingUnitId = null;
        $this->editingParentId = $parentId;
        $this->unitTitle = '';
        $this->unitDescription = '';
        $parent = CourseUnit::findOrFail($parentId);
        $this->unitType = $parent->type; // topic inherits block type
        $this->unitIsRequired = true;
        $this->unitDurationMinutes = null;
        $this->unitPosition = 0;
    }

    public function editUnit(int $unitId)
    {
        $unit = CourseUnit::findOrFail($unitId);
        $this->editingUnitId = $unit->id;
        $this->editingParentId = $unit->parent_id;
        $this->unitTitle = (string)$unit->title;
        $this->unitDescription = (string)($unit->description ?? '');
        $this->unitType = (string)$unit->type;
        $this->unitIsRequired = (bool)$unit->is_required;
        $this->unitDurationMinutes = $unit->duration_minutes ? (int)$unit->duration_minutes : null;
        $this->unitPosition = (int)($unit->position ?? 0);
    }

    public function saveUnit()
    {
        $data = $this->validate([
            'unitTitle' => 'required|string|min:2',
            'unitDescription' => 'nullable|string',
            'unitType' => 'required|in:theory,practice_flight,practice_lab,simulator',
            'unitIsRequired' => 'boolean',
            'unitDurationMinutes' => 'nullable|integer|min:0',
            'unitPosition' => 'integer|min:0',
        ]);

        $course = Course::orderBy('id')->firstOrFail();
        $payload = [
            'course_id' => $course->id,
            'title' => $data['unitTitle'],
            'description' => $data['unitDescription'] ?? null,
            'type' => $data['unitType'],
            'is_required' => (bool)$data['unitIsRequired'],
            'duration_minutes' => $data['unitDurationMinutes'] ?? null,
            'position' => $data['unitPosition'],
            'parent_id' => $this->editingParentId,
        ];

        if ($this->editingUnitId) {
            $unit = CourseUnit::findOrFail($this->editingUnitId);
            $unit->update($payload);
            session()->flash('success', 'Jednostka zaktualizowana.');
        } else {
            CourseUnit::create($payload);
            session()->flash('success', 'Jednostka dodana.');
        }

        $this->resetUnitEditor();
    }

    public function deleteUnit(int $unitId)
    {
        $unit = CourseUnit::findOrFail($unitId);
        $unit->delete();
        session()->flash('success', 'Jednostka usunięta.');
        $this->resetUnitEditor();
    }

    public function moveUnit(int $unitId, string $direction)
    {
        $unit = CourseUnit::findOrFail($unitId);
        $delta = $direction === 'up' ? -1 : 1;
        $unit->position = max(0, (int)($unit->position ?? 0) + $delta);
        $unit->save();
        session()->flash('success', 'Zmieniono pozycję.');
    }

    public function resetUnitEditor()
    {
        $this->reset(['editingUnitId','editingParentId','unitTitle','unitDescription','unitType','unitIsRequired','unitDurationMinutes','unitPosition']);
        $this->unitType = 'theory';
        $this->unitIsRequired = true;
        $this->unitDurationMinutes = null;
        $this->unitPosition = 0;
    }

    public function startInstanceCreator()
    {
        $this->showInstanceCreator = true;
        $this->selectedTemplateId = null;
        $this->selectedGroupId = null;
        $this->manualStudentIds = [];
    }

    public function createCourseInstance()
    {
        $this->validate([
            'selectedTemplateId' => 'required|exists:courses,id',
            'selectedGroupId' => 'nullable|exists:groups,id',
        ]);

        $template = Course::where('is_template', true)->findOrFail($this->selectedTemplateId);

        // Utwórz nową instancję kursu
        $instance = Course::create([
            'name' => $template->name . ' - ' . now()->format('Y-m-d'),
            'description' => $template->description,
            'active' => true,
            'is_template' => false,
            'template_id' => $template->id,
            'default_flight_hours_required' => $template->default_flight_hours_required,
            'default_sim_hours_required' => $template->default_sim_hours_required,
            'require_lab' => $template->require_lab,
        ]);

        // Skopiuj strukturę jednostek
        $instance->copyUnitsFromTemplate($template);

        // Przypisz uczniów z wybranej grupy
        if ($this->selectedGroupId) {
            $group = Group::findOrFail($this->selectedGroupId);
            $students = User::where('role', 'student')
                ->where('group_id', $group->id)
                ->where('active', true)
                ->get();

            foreach ($students as $student) {
                $instance->studentCourses()->create([
                    'user_id' => $student->id,
                    'group_id' => $group->id,
                    'flight_hours_required' => $instance->default_flight_hours_required,
                    'sim_hours_required' => $instance->default_sim_hours_required,
                    'require_lab' => $instance->require_lab,
                    'status' => 'active',
                    'start_date' => now(),
                ]);
            }
        }

        // Przypisz ręcznie dodanych uczniów (jeśli będzie implementacja)
        // TODO: implement manual student selection

        session()->flash('success', 'Utworzono instancję kursu i przypisano ' . $instance->studentCourses()->count() . ' uczniów.');
        $this->showInstanceCreator = false;
    }

    public function cancelInstanceCreator()
    {
        $this->showInstanceCreator = false;
        $this->selectedTemplateId = null;
        $this->selectedGroupId = null;
        $this->manualStudentIds = [];
    }

    public function recalculateHours(int $courseId)
    {
        $course = Course::findOrFail($courseId);
        $course->calculateHours();
        session()->flash('success', 'Przeliczono godziny kursu.');
    }
}
