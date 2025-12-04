<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseMaterial;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app.sidebar')]
class CourseMaterials extends Component
{
    use WithFileUploads;

    public ?int $courseId = 1;
    public string $search = '';
    public string $filterType = '';
    public string $filterUnit = '';

    // Material editor
    public bool $showMaterialEditor = false;
    public ?int $editingMaterialId = null;
    public ?int $selectedUnitId = null;
    public string $materialTitle = '';
    public string $materialDescription = '';
    public string $materialType = 'pdf';
    public $materialFile = null;
    public string $materialUrl = '';

    public function mount()
    {
        $course = Course::find($this->courseId);
        if (!$course) {
            $course = Course::first();
            $this->courseId = $course?->id;
        }
    }

    public function render()
    {
        $course = Course::find($this->courseId);

        $units = $course ? CourseUnit::where('course_id', $course->id)
            ->with(['materials' => function($query) {
                $query->with('uploadedBy');

                // Apply search filter
                if ($this->search) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                }

                // Apply type filter
                if ($this->filterType) {
                    $query->where('type', $this->filterType);
                }
            }])
            ->orderBy('parent_id')
            ->orderBy('position')
            ->get() : collect();

        // Apply unit filter
        if ($this->filterUnit) {
            $units = $units->filter(function($unit) {
                return $unit->id == $this->filterUnit ||
                       $unit->parent_id == $this->filterUnit ||
                       $unit->materials->isNotEmpty();
            });
        }

        $blocks = $units->whereNull('parent_id');

        return view('livewire.admin.course-materials', [
            'course' => $course,
            'blocks' => $blocks,
            'units' => $units,
        ]);
    }

    public function openMaterialEditor(?int $materialId = null, ?int $unitId = null)
    {
        $this->resetMaterialForm();
        $this->selectedUnitId = $unitId;

        if ($materialId) {
            $material = CourseMaterial::findOrFail($materialId);
            $this->editingMaterialId = $material->id;
            $this->selectedUnitId = $material->course_unit_id;
            $this->materialTitle = $material->title;
            $this->materialDescription = $material->description ?? '';
            $this->materialType = $material->type;
            $this->materialUrl = $material->url_or_file_path;
        }

        $this->showMaterialEditor = true;
    }

    public function saveMaterial()
    {
        try {
            $rules = [
            'materialTitle' => 'required|string|min:3',
            'materialDescription' => 'nullable|string|max:1000',
            'materialType' => 'required|in:pdf,video_link,external_link',
            'selectedUnitId' => 'required|exists:course_units,id',
        ];

        if ($this->materialType === 'pdf' && !$this->editingMaterialId) {
            $rules['materialFile'] = 'required|file|mimes:pdf|max:51200';
        } elseif ($this->materialType !== 'pdf') {
            $rules['materialUrl'] = 'required|url';
        }

        $data = $this->validate($rules);

        if ($this->editingMaterialId) {
            $material = CourseMaterial::findOrFail($this->editingMaterialId);
            $material->title = $data['materialTitle'];
            $material->description = $this->materialDescription;
            $material->type = $data['materialType'];
            $material->course_unit_id = $data['selectedUnitId'];

            if ($this->materialType !== 'pdf' && $this->materialUrl) {
                $material->url_or_file_path = $this->materialUrl;
            }
        } else {
            $material = new CourseMaterial();
            $material->course_unit_id = $data['selectedUnitId'];
            $material->title = $data['materialTitle'];
            $material->description = $this->materialDescription;
            $material->type = $data['materialType'];

            $user = auth()->guard('web')->user();
            $material->uploaded_by_id = $user?->id;
            $material->is_approved = in_array($user?->role, ['admin', 'instructor']);

            if ($this->materialType === 'pdf' && $this->materialFile) {
                $path = $this->materialFile->store('course_materials', 'public');
                $material->url_or_file_path = $path;
            } else {
                $material->url_or_file_path = $data['materialUrl'];
            }
        }

        $material->save();

        $message = $this->editingMaterialId ? 'Materiał zaktualizowany.' : 'Materiał dodany.';
        $this->dispatch('notify', type: 'success', message: $message);
        $this->closeMaterialEditor();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', type: 'error', message: 'Błąd walidacji: ' . implode(' ', $e->validator->errors()->all()));
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Błąd podczas zapisywania materiału.');
            throw $e;
        }
    }

    public function deleteMaterial(int $materialId)
    {
        $material = CourseMaterial::findOrFail($materialId);

        if ($material->type === 'pdf') {
            Storage::disk('public')->delete($material->url_or_file_path);
        }

        $material->delete();
        $this->dispatch('notify', type: 'success', message: 'Materiał usunięty.');
    }

    public function closeMaterialEditor()
    {
        $this->showMaterialEditor = false;
        $this->resetMaterialForm();
    }

    private function resetMaterialForm()
    {
        $this->reset(['editingMaterialId', 'selectedUnitId', 'materialTitle', 'materialDescription', 'materialType', 'materialFile', 'materialUrl']);
        $this->materialType = 'pdf';
    }
}

