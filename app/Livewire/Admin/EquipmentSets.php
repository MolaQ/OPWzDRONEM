<?php

namespace App\Livewire\Admin;

use App\Models\Equipment;
use App\Models\EquipmentSet;
use App\Services\BarcodeResolver;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app.sidebar')]
class EquipmentSets extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingSetId = null;
    public $name = '';
    public $description = '';
    public $selectedEquipment = [];
    public $active = true;
    public $search = '';

    // Equipment search/scan
    public $equipmentSearch = '';
    public $equipmentSuggestions = [];
    public $showEquipmentSuggestions = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'selectedEquipment' => 'required|array|min:1',
            'selectedEquipment.*' => 'exists:equipments,id',
            'active' => 'boolean',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['editingSetId', 'name', 'description', 'selectedEquipment', 'active']);
        $this->active = true;
        $this->showModal = true;
    }

    public function edit($setId)
    {
        $set = EquipmentSet::with('equipments')->findOrFail($setId);

        // Check if set is currently rented
        if ($set->rentals()->whereNull('returned_at')->exists()) {
            $this->dispatch('notify', type: 'error', message: 'Nie można edytować zestawu, który jest obecnie wypożyczony');
            return;
        }

        $this->editingSetId = $set->id;
        $this->name = $set->name;
        $this->description = $set->description;
        $this->selectedEquipment = $set->equipments->pluck('id')->toArray();
        $this->active = $set->active;
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editingSetId) {
            $set = EquipmentSet::findOrFail($this->editingSetId);
            $set->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'active' => $validated['active'],
            ]);
            $message = 'Zestaw został zaktualizowany';
        } else {
            // Create with temporary barcode
            $set = EquipmentSet::create([
                'barcode' => 'Z-TEMP-' . time(),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'active' => $validated['active'],
            ]);

            // Update with proper barcode
            $set->update([
                'barcode' => BarcodeResolver::generateSetBarcode($set->id)
            ]);

            $message = 'Zestaw został utworzony';
        }

        // Sync equipment
        $set->equipments()->sync($validated['selectedEquipment']);

        $this->showModal = false;
        $this->reset(['editingSetId', 'name', 'description', 'selectedEquipment', 'active']);

        $this->dispatch('notify', type: 'success', message: $message);
        $this->dispatch('set-saved');
    }

    public function delete($setId)
    {
        $set = EquipmentSet::findOrFail($setId);

        // Check if set has active rentals
        if ($set->rentals()->whereNull('returned_at')->exists()) {
            $this->dispatch('notify', type: 'error', message: 'Nie można usunąć zestawu, który jest obecnie wypożyczony');
            return;
        }

        $set->delete();
        $this->dispatch('notify', type: 'success', message: 'Zestaw został usunięty');
    }

    public function toggleActive($setId)
    {
        $set = EquipmentSet::findOrFail($setId);
        $set->update(['active' => !$set->active]);

        $this->dispatch('notify', type: 'success', message: $set->active ? 'Zestaw aktywowany' : 'Zestaw dezaktywowany');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingSetId', 'name', 'description', 'selectedEquipment', 'active', 'equipmentSearch', 'equipmentSuggestions', 'showEquipmentSuggestions']);
        $this->resetValidation();
    }

    public function updatedEquipmentSearch()
    {
        $query = strtoupper(trim($this->equipmentSearch));

        if (empty($query)) {
            $this->equipmentSuggestions = [];
            $this->showEquipmentSuggestions = false;
            return;
        }

        // Check if it's a valid barcode format
        if (preg_match('/^E\d{10}$/', $query)) {
            $this->addEquipmentByBarcode($query);
            return;
        }

        // Search for equipment by barcode or name
        $this->equipmentSuggestions = Equipment::query()
            ->where(function($q) use ($query) {
                $q->where('barcode', 'LIKE', "%$query%")
                  ->orWhere('name', 'LIKE', "%$query%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->toArray();

        $this->showEquipmentSuggestions = !empty($this->equipmentSuggestions);
    }

    public function addEquipmentByBarcode($barcode)
    {
        $equipment = Equipment::where('barcode', $barcode)->first();

        if (!$equipment) {
            $this->dispatch('notify', type: 'error', message: 'Nie znaleziono sprzętu o kodzie: ' . $barcode);
            return;
        }

        if (!in_array($equipment->id, $this->selectedEquipment)) {
            $this->selectedEquipment[] = $equipment->id;
        }

        $this->equipmentSearch = '';
        $this->equipmentSuggestions = [];
        $this->showEquipmentSuggestions = false;
    }

    public function selectEquipmentFromSuggestion($equipmentId)
    {
        if (!in_array($equipmentId, $this->selectedEquipment)) {
            $this->selectedEquipment[] = $equipmentId;
        }

        $this->equipmentSearch = '';
        $this->equipmentSuggestions = [];
        $this->showEquipmentSuggestions = false;
    }

    public function removeEquipment($equipmentId)
    {
        $this->selectedEquipment = array_values(array_diff($this->selectedEquipment, [$equipmentId]));
    }

    public function render()
    {
        $sets = EquipmentSet::query()
            ->with(['equipments'])
            ->withCount('equipments')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('barcode', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        $availableEquipment = Equipment::query()
            ->orderBy('name')
            ->get();

        return view('livewire.admin.equipment-sets', [
            'sets' => $sets,
            'availableEquipment' => $availableEquipment,
        ]);
    }
}
