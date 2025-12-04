<?php

namespace App\Livewire\Admin;

use App\Models\Equipment as EquipmentModel;
use App\Services\BarcodeResolver;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app.sidebar')]
class Equipment extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingEquipmentId = null;
    public $name = '';
    public $model = '';
    public $category = '';
    public $status = 'available';
    public $description = '';
    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'status' => 'required|string|in:available,rented,maintenance,retired',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['editingEquipmentId', 'name', 'model', 'category', 'status', 'description']);
        $this->status = 'available';
        $this->showModal = true;
    }

    public function edit($equipmentId)
    {
        $equipment = EquipmentModel::findOrFail($equipmentId);

        // Check if equipment is currently rented
        if ($equipment->rentals()->whereNull('returned_at')->exists()) {
            $this->dispatch('notify', type: 'error', message: 'Nie można edytować sprzętu, który jest obecnie wypożyczony');
            return;
        }

        $this->editingEquipmentId = $equipment->id;
        $this->name = $equipment->name;
        $this->model = $equipment->model;
        $this->category = $equipment->category;
        $this->status = $equipment->status;
        $this->description = $equipment->description;
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editingEquipmentId) {
            $equipment = EquipmentModel::findOrFail($this->editingEquipmentId);
            $equipment->update($validated);
            $message = 'Sprzęt został zaktualizowany';
        } else {
            // Create with temporary barcode
            $equipment = EquipmentModel::create([
                'barcode' => 'E-TEMP-' . time(),
                'name' => $validated['name'],
                'model' => $validated['model'],
                'category' => $validated['category'],
                'status' => $validated['status'],
                'description' => $validated['description'],
            ]);

            // Update with proper barcode
            $equipment->update([
                'barcode' => BarcodeResolver::generateEquipmentBarcode($equipment->id)
            ]);

            $message = 'Sprzęt został utworzony';
        }

        $this->showModal = false;
        $this->reset(['editingEquipmentId', 'name', 'model', 'category', 'status', 'description']);

        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function delete($equipmentId)
    {
        $equipment = EquipmentModel::findOrFail($equipmentId);

        // Check if equipment has active rentals
        if ($equipment->rentals()->whereNull('returned_at')->exists()) {
            $this->dispatch('notify', type: 'error', message: 'Nie można usunąć sprzętu, który jest obecnie wypożyczony');
            return;
        }

        $equipment->delete();
        $this->dispatch('notify', type: 'success', message: 'Sprzęt został usunięty');
    }

    public function changeStatus($equipmentId, $newStatus)
    {
        $equipment = EquipmentModel::findOrFail($equipmentId);
        $equipment->update(['status' => $newStatus]);

        $statusLabels = [
            'available' => 'Dostępny',
            'rented' => 'Wypożyczony',
            'maintenance' => 'W naprawie',
            'retired' => 'Wycofany',
        ];

        $this->dispatch('notify', type: 'success', message: 'Status zmieniono na: ' . $statusLabels[$newStatus]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingEquipmentId', 'name', 'model', 'category', 'status', 'description']);
        $this->resetValidation();
    }

    public function render()
    {
        $equipments = EquipmentModel::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'LIKE', "%{$this->search}%")
                      ->orWhere('barcode', 'LIKE', "%{$this->search}%")
                      ->orWhere('model', 'LIKE', "%{$this->search}%")
                      ->orWhere('category', 'LIKE', "%{$this->search}%");
                });
            })
            ->withCount('rentals')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.equipment', [
            'equipments' => $equipments,
        ]);
    }
}
