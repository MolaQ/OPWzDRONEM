<?php

namespace App\Livewire\Admin;

use App\Models\Equipment;
use App\Models\EquipmentReservation;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EquipmentReservationForm extends Component
{
    public Equipment $equipment;
    public $user_id = '';
    public $group_id = '';
    public $reserved_from = '';
    public $reserved_until = '';
    public $reason = '';
    public $notes = '';
    public $showErrors = false;

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'group_id' => 'nullable|exists:groups,id',
            'reserved_from' => 'required|date_format:Y-m-d\TH:i',
            'reserved_until' => 'required|date_format:Y-m-d\TH:i|after:reserved_from',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    protected function messages()
    {
        return [
            'user_id.required' => 'Użytkownik jest wymagany',
            'reserved_from.required' => 'Data rozpoczęcia rezerwacji jest wymagana',
            'reserved_until.required' => 'Data zakończenia rezerwacji jest wymagana',
            'reserved_until.after' => 'Data zakończenia musi być po dacie rozpoczęcia',
        ];
    }

    public function mount(Equipment $equipment)
    {
        $this->equipment = $equipment;
        $this->user_id = Auth::id();
    }

    public function save()
    {
        try {
            $validated = $this->validate();

            // Check if equipment is available during the period
            $conflict = EquipmentReservation::where('equipment_id', $this->equipment->id)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('reserved_from', [$validated['reserved_from'], $validated['reserved_until']])
                ->orWhereBetween('reserved_until', [$validated['reserved_from'], $validated['reserved_until']])
                ->exists();

            if ($conflict) {
                $this->addError('reserved_from', 'Sprzęt jest niedostępny w wybranym terminie');
                $this->showErrors = true;
                return;
            }

            $reservation = EquipmentReservation::create([
                'equipment_id' => $this->equipment->id,
                'user_id' => $validated['user_id'],
                'group_id' => $validated['group_id'] ? (int) $validated['group_id'] : null,
                'reserved_from' => $validated['reserved_from'],
                'reserved_until' => $validated['reserved_until'],
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            // Auto-set equipment status to reserved if currently available
            if ($this->equipment->status === 'available') {
                $this->equipment->update(['status' => 'reserved']);
            }

            $this->dispatch('notify', type: 'success', message: 'Rezerwacja została utworzona');
            $this->dispatch('close-modal');
            $this->dispatch('refresh-data');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Błąd: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $users = \App\Models\User::orderBy('name')->get();
        $groups = \App\Models\Group::orderBy('name')->get();

        return view('livewire.admin.equipment-reservation-form', [
            'users' => $users,
            'groups' => $groups,
        ]);
    }
}
