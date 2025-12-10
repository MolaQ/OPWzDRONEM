<?php

namespace App\Livewire\Admin;

use App\Models\EquipmentSet;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class EquipmentSetDetail extends Component
{
    public EquipmentSet $equipmentSet;
    public bool $showReservationModal = false;

    protected $listeners = ['close-modal' => 'closeReservationModal', 'refresh-data' => 'refreshData'];

    public function mount($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->can('equipment.view')) {
            abort(403, 'Brak uprawnień.');
        }

        $this->equipmentSet = EquipmentSet::findOrFail($id);
    }

    public function openReservationModal()
    {
        $this->showReservationModal = true;
    }

    public function closeReservationModal()
    {
        $this->showReservationModal = false;
    }

    public function refreshData()
    {
        $this->equipmentSet = $this->equipmentSet->fresh(['equipments']);
    }

    public function render()
    {
        return view('livewire.admin.equipment-set-detail', [
            'equipmentSet' => $this->equipmentSet,
        ]);
    }
}
