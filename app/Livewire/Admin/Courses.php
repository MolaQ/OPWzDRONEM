<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseMaterial;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app.sidebar')]
class Courses extends Component
{
    use WithFileUploads;

    // Filters - regular properties for wire:model.live
    public ?string $search = null;
    public bool $filterRequiredOnly = false;
    public bool $filterHasMaterials = false;
    public bool $filterWithoutMaterials = false;

    // Block/Topic editor state
    public ?int $editingUnitId = null;
    public ?int $editingParentId = null;
    public string $unitTitle = '';
    public string $unitDescription = '';
    public string $unitType = 'theory';
    public bool $unitIsRequired = true;
    public ?int $unitDurationMinutes = null;
    public int $unitPosition = 0;

    // Course editor state
    public bool $showCourseEditor = false;
    public string $courseTitle = '';
    public ?int $courseId = null;

    // Materials state
    public ?int $materialEditingUnitId = null;
    public string $materialTitle = '';
    public string $materialType = 'pdf'; // pdf, video_link, external_link
    public $materialFile = null;
    public string $materialUrl = '';
    public array $unitMaterials = [];

    public function mount()
    {
        // Default course ID for 'OPW z Dronem' (ID: 1 from seeder)
        if (!$this->courseId) {
            $this->courseId = 1;
        }

        // Load course title
        $course = Course::find($this->courseId);
        if ($course) {
            $this->courseTitle = $course->name;
        }
    }

    public function render()
    {
        $course = $this->courseId ? Course::find($this->courseId) : null;
        $blocks = collect();

        if ($course) {
            // Zaawansowana logika wyszukiwania
            $searchTerm = trim($this->search);

            // Jeśli są filtry dla materiałów, najpierw znajdujemy zagadnienia z materiałami
            $unitIdsWithMaterials = null;
            if ($this->filterHasMaterials || $this->filterWithoutMaterials) {
                $unitsWithMaterials = CourseUnit::whereHas('approvedMaterials')
                    ->pluck('id')
                    ->toArray();

                if ($this->filterHasMaterials && !$this->filterWithoutMaterials) {
                    $unitIdsWithMaterials = $unitsWithMaterials;
                } elseif ($this->filterWithoutMaterials && !$this->filterHasMaterials) {
                    // Wszystkie zagadnienia kursu oprócz tych z materiałami
                    $allUnitIds = CourseUnit::where('course_id', $course->id)->pluck('id')->toArray();
                    $unitIdsWithMaterials = array_diff($allUnitIds, $unitsWithMaterials);
                }
                // Jeśli są oba zaznaczone - nie filtrujemy (pokazujemy wszystkie)
            }

            $query = CourseUnit::query()
                ->where('course_id', $course->id)
                ->whereNull('parent_id');

            // Filtry wymagalności
            if ($this->filterRequiredOnly) {
                $query->where('is_required', true);
            }

            // Zaawansowane wyszukiwanie po tekście
            if ($searchTerm !== '') {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      // Wyszukaj również w dzieciach (zagadnieniach)
                      ->orWhereHas('children', function($childQ) use ($searchTerm) {
                          $childQ->where('title', 'like', '%' . $searchTerm . '%')
                                 ->orWhere('description', 'like', '%' . $searchTerm . '%');
                      })
                      // Wyszukaj w materiałach
                      ->orWhereHas('children.approvedMaterials', function($matQ) use ($searchTerm) {
                          $matQ->where('title', 'like', '%' . $searchTerm . '%')
                               ->orWhere('type', 'like', '%' . $searchTerm . '%');
                      });
                });
            }

            $blocks = $query->orderBy('position')->orderBy('id')->get();

            // Załaduj dzieci z filtrami
            $blocks->load(['children' => function($childQ) use ($searchTerm, $unitIdsWithMaterials) {
                // NIE filtrujemy dzieci po kategorii - dziedziczą kategorię od bloku!

                if ($this->filterRequiredOnly) {
                    $childQ->where('is_required', true);
                }

                // Filtr dla materiałów
                if ($unitIdsWithMaterials !== null) {
                    $childQ->whereIn('id', $unitIdsWithMaterials);
                }

                // Zaawansowane wyszukiwanie w zagadnieniach
                if ($searchTerm !== '') {
                    $childQ->where(function($q) use ($searchTerm) {
                        $q->where('title', 'like', '%' . $searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $searchTerm . '%')
                          // Wyszukaj w materiałach zagadnienia
                          ->orWhereHas('approvedMaterials', function($matQ) use ($searchTerm) {
                              $matQ->where('title', 'like', '%' . $searchTerm . '%')
                                   ->orWhere('type', 'like', '%' . $searchTerm . '%');
                          });
                    });
                }

                $childQ->orderBy('position')->orderBy('id');
            }]);
        }

        return view('livewire.admin.courses', [
            'course' => $course,
            'blocks' => $blocks,
        ]);
    }

    public function editCourse()
    {
        if ($this->courseId) {
            $course = Course::find($this->courseId);
            if ($course) {
                $this->courseTitle = $course->name;
                $this->showCourseEditor = true;
            }
        }
    }

    public function saveCourse()
    {
        $this->validate([
            'courseTitle' => 'required|string|min:3|max:255',
        ]);

        if ($this->courseId) {
            $course = Course::find($this->courseId);
            if ($course) {
                $course->update(['name' => $this->courseTitle]);
                // Ensure the property is synced
                $this->courseTitle = $course->name;
                $this->dispatch('notify', type: 'success', message: 'Kurs zaktualizowany.');
                $this->showCourseEditor = false;
            }
        }
    }

    public function closeCourseEditor()
    {
        $this->showCourseEditor = false;
    }

    public function startCreateBlock()
    {
        $this->editingUnitId = -1; // Znacznik dla nowego bloku (ujemne ID)
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
        $this->unitType = 'theory'; // Zawsze teoria
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
        $this->loadMaterialsForUnit($unitId);
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

        // Jeśli tworzymy nową jednostkę i pozycja nie jest ustawiona, oblicz ją
        $position = $data['unitPosition'];
        $isNewUnit = $this->editingUnitId === null || $this->editingUnitId <= 0;
        if ($isNewUnit) {
            if ($this->editingParentId) {
                // Dla nowego zagadnienia - maksymalna pozycja wśród dzieci
                $maxPosition = CourseUnit::where('course_id', $course->id)
                    ->where('parent_id', $this->editingParentId)
                    ->max('position') ?? -1;
                $position = $maxPosition + 1;
            } else {
                // Dla nowego bloku - maksymalna pozycja wśród bloków
                $maxPosition = CourseUnit::where('course_id', $course->id)
                    ->whereNull('parent_id')
                    ->max('position') ?? -1;
                $position = $maxPosition + 1;
            }
        }

        $payload = [
            'course_id' => $course->id,
            'title' => $data['unitTitle'],
            'description' => $data['unitDescription'] ?? null,
            'type' => $data['unitType'],
            'is_required' => (bool)$data['unitIsRequired'],
            'duration_minutes' => $data['unitDurationMinutes'] ?? null,
            'position' => $position,
            'parent_id' => $this->editingParentId,
        ];

        if ($this->editingUnitId && $this->editingUnitId > 0) {
            $unit = CourseUnit::findOrFail($this->editingUnitId);
            $unit->update($payload);
            $this->dispatch('notify', type: 'success', message: 'Jednostka zaktualizowana.');
        } else {
            $newUnit = CourseUnit::create($payload);
            $this->dispatch('notify', type: 'success', message: 'Jednostka dodana.');

            // Normalizuj pozycje wszystkich rodzeństwa po dodaniu
            $this->normalizeSiblingPositions($newUnit);
        }

        $this->resetUnitEditor();
    }

    private function normalizeSiblingPositions(CourseUnit $unit)
    {
        // Pobierz wszystkie rodzeństwo i nadaj im pozycje od 0
        $siblings = CourseUnit::where('course_id', $unit->course_id)
            ->where('parent_id', $unit->parent_id)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        foreach ($siblings as $index => $sibling) {
            if ($sibling->position !== $index) {
                $sibling->position = $index;
                $sibling->save();
            }
        }
    }

    public function deleteUnit(int $unitId)
    {
        $unit = CourseUnit::findOrFail($unitId);
        $unit->delete();
        $this->dispatch('notify', type: 'success', message: 'Jednostka usunięta.');
        $this->resetUnitEditor();
    }

    public function moveUnit(int $unitId, string $direction)
    {
        $unit = CourseUnit::findOrFail($unitId);

        // Pobierz rodzeństwo (elementy na tym samym poziomie)
        $siblings = CourseUnit::where('course_id', $unit->course_id)
            ->where('parent_id', $unit->parent_id)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        // Znajdź indeks aktualnego elementu
        $currentIndex = $siblings->search(fn($item) => $item->id === $unit->id);

        if ($currentIndex === false) {
            $this->dispatch('notify', type: 'error', message: 'Element nie znaleziony.');
            return;
        }

        // Określ nowy indeks
        $newIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

        // Sprawdź czy nowy indeks jest prawidłowy
        if ($newIndex < 0 || $newIndex >= $siblings->count()) {
            $this->dispatch('notify', type: 'info', message: 'Element jest już na końcu listy.');
            return;
        }

        // Zamień pozycje
        $targetUnit = $siblings[$newIndex];
        $tempPosition = $unit->position;
        $unit->position = $targetUnit->position;
        $targetUnit->position = $tempPosition;

        $unit->save();
        $targetUnit->save();

        $this->dispatch('notify', type: 'success', message: 'Zmieniono pozycję.');
    }

    public function resetUnitEditor()
    {
        $this->reset(['editingUnitId','editingParentId','unitTitle','unitDescription','unitType','unitIsRequired','unitDurationMinutes','unitPosition']);
        $this->unitType = 'theory';
        $this->unitIsRequired = true;
        $this->unitDurationMinutes = null;
        $this->unitPosition = 0;
        $this->resetMaterialEditor();
    }

    // =============== MATERIALS METHODS ===============

    public function loadMaterialsForUnit(int $unitId)
    {
        $materials = CourseMaterial::where('course_unit_id', $unitId)->orderBy('created_at', 'desc')->get();
        $this->unitMaterials = $materials->map(fn($m) => [
            'id' => $m->id,
            'title' => $m->title,
            'description' => $m->description,
            'type' => $m->type,
            'url' => $m->url_or_file_path,
            'uploaded_by' => $m->uploadedBy->name ?? 'System',
            'is_approved' => $m->is_approved,
        ])->toArray();
    }

    public function startAddMaterial(int $unitId)
    {
        // Clear form fields but keep unit ID
        $this->reset(['materialTitle', 'materialType', 'materialFile', 'materialUrl']);
        $this->materialType = 'pdf';
        $this->materialEditingUnitId = $unitId;
        $this->loadMaterialsForUnit($unitId);
    }

    public function saveMaterial()
    {
        $rules = [
            'materialTitle' => 'required|string|min:3',
            'materialType' => 'required|in:pdf,video_link,external_link',
        ];

        if ($this->materialType === 'pdf') {
            $rules['materialFile'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:51200'; // 50MB
        } else {
            $rules['materialUrl'] = 'required|url';
        }

        $data = $this->validate($rules);

        $material = new CourseMaterial();
        $material->course_unit_id = $this->materialEditingUnitId;
        $material->title = $data['materialTitle'];
        $material->type = $data['materialType'];

        $user = auth()->guard('web')->user();
        $material->uploaded_by_id = $user?->id;
        $material->is_approved = $user ? Gate::forUser($user)->allows('course-materials.approve') : false;

        if ($this->materialType === 'pdf' && $this->materialFile) {
            $path = $this->materialFile->store('course_materials', 'public');
            $material->url_or_file_path = $path;
        } else {
            $material->url_or_file_path = $data['materialUrl'];
        }

        $material->save();

        $unitId = $this->materialEditingUnitId; // Save before reset

        $this->dispatch('notify', type: 'success', message: 'Materiał został ' . ($material->is_approved ? 'zatwierdzony' : 'wysłany do zatwierdzenia') . '.');
        $this->resetMaterialEditor();
        $this->loadMaterialsForUnit($unitId); // Use saved ID
    }

    public function deleteMaterial(int $materialId)
    {
        $material = CourseMaterial::findOrFail($materialId);
        $unitId = $material->course_unit_id;

        if ($material->type === 'pdf') {
            Storage::disk('public')->delete($material->url_or_file_path);
        }

        $material->delete();
        $this->dispatch('notify', type: 'success', message: 'Materiał usunięty.');
        $this->loadMaterialsForUnit($unitId);
    }

    public function approveMaterial(int $materialId)
    {
        $material = CourseMaterial::findOrFail($materialId);
        $material->is_approved = true;
        $material->rejection_reason = null;
        $material->save();
        $this->dispatch('notify', type: 'success', message: 'Materiał zatwierdzony.');
        $this->loadMaterialsForUnit($material->course_unit_id);
    }

    public function rejectMaterial(int $materialId, string $reason = '')
    {
        $material = CourseMaterial::findOrFail($materialId);
        $material->is_approved = false;
        $material->rejection_reason = $reason ?: 'Odrzucone przez admina';
        $material->save();
        $this->dispatch('notify', type: 'success', message: 'Materiał odrzucony.');
        $this->loadMaterialsForUnit($material->course_unit_id);
    }

    public function resetMaterialEditor()
    {
        $this->reset(['materialEditingUnitId', 'materialTitle', 'materialType', 'materialFile', 'materialUrl']);
        $this->materialType = 'pdf';
    }
}

