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

    public function mount($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->can('equipment.view')) {
            abort(403, 'Brak uprawnień.');
        }

        $this->equipment = Equipment::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.equipment-detail', [
            'equipment' => $this->equipment,
        ]);
    }
}
