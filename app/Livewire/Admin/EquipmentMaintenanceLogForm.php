<?php

namespace App\Livewire\Admin;

use App\Models\Equipment;
use App\Models\EquipmentMaintenanceLog;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EquipmentMaintenanceLogForm extends Component
{
    public Equipment $equipment;
    public $type = '';
    public $description = '';
    public $findings = '';
    public $actions_taken = '';
    public $cost = '';
    public $next_maintenance_recommended = '';
    public $showErrors = false;

    protected function rules()
    {
        return [
            'type' => 'required|in:preventive_maintenance,repair,inspection,calibration,battery_replacement,cleaning,software_update,other',
            'description' => 'nullable|string|max:1000',
            'findings' => 'nullable|string|max:1000',
            'actions_taken' => 'nullable|string|max:1000',
            'cost' => 'nullable|numeric|min:0',
            'next_maintenance_recommended' => 'nullable|string|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'type.required' => 'Typ serwisu jest wymagany',
            'cost.numeric' => 'Koszt musi być liczbą',
        ];
    }

    public function mount(Equipment $equipment)
    {
        $this->equipment = $equipment;
    }

    public function save()
    {
        try {
            $validated = $this->validate();

            EquipmentMaintenanceLog::create([
                'equipment_id' => $this->equipment->id,
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
                'findings' => $validated['findings'] ?? null,
                'actions_taken' => $validated['actions_taken'] ?? null,
                'cost' => $validated['cost'] ? floatval($validated['cost']) : null,
                'next_maintenance_recommended' => $validated['next_maintenance_recommended'] ?? null,
                'performed_by_user_id' => Auth::id(),
                'performed_at' => now(),
            ]);

            // Update equipment's last maintenance date
            $this->equipment->update([
                'last_maintenance_date' => now(),
                'status' => 'available',
            ]);

            $this->dispatch('notify', type: 'success', message: 'Wpis serwisowania został zapisany');
            $this->dispatch('close-modal');
            $this->dispatch('refresh-data');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Błąd: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.equipment-maintenance-log-form');
    }
}
