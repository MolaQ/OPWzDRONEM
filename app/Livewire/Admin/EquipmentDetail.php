<?php

namespace App\Livewire\Admin;

use App\Models\Equipment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class EquipmentDetail extends Component
{
    public Equipment $equipment;
    public $maintenanceLogs = [];
    public $reservations = [];
    public $showReservationModal = false;
    public $showMaintenanceModal = false;

    public function mount($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->can('equipment.view')) {
            abort(403, 'Brak uprawnień.');
        }

        $this->equipment = Equipment::findOrFail($id);
        $this->loadMaintenanceLogs();
        $this->loadReservations();
    }

    public function loadMaintenanceLogs()
    {
        $this->maintenanceLogs = $this->equipment
            ->maintenanceLogs()
            ->with('performedBy')
            ->orderByDesc('performed_at')
            ->get()
            ->toArray();
    }

    public function loadReservations()
    {
        $this->reservations = $this->equipment
            ->reservations()
            ->with(['user', 'group', 'confirmedBy'])
            ->orderByDesc('reserved_from')
            ->get()
            ->toArray();
    }

    public function openReservationModal()
    {
        $this->showReservationModal = true;
    }

    public function closeReservationModal()
    {
        $this->showReservationModal = false;
    }

    public function openMaintenanceModal()
    {
        $this->showMaintenanceModal = true;
    }

    public function closeMaintenanceModal()
    {
        $this->showMaintenanceModal = false;
    }

    public function refreshData()
    {
        $this->equipment = $this->equipment->fresh();
        $this->loadMaintenanceLogs();
        $this->loadReservations();
    }

    public function render()
    {
        return view('livewire.admin.equipment-detail', [
            'equipment' => $this->equipment,
            'maintenanceLogs' => $this->maintenanceLogs,
            'reservations' => $this->reservations,
        ]);
    }
}
