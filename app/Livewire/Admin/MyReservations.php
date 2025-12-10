<?php

namespace App\Livewire\Admin;

use App\Models\EquipmentReservation;
use App\Models\Equipment;
use App\Models\Rental;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app.sidebar')]
class MyReservations extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $search = '';
    public $sortBy = 'reserved_from';
    public $sortDirection = 'desc';

    protected $queryString = ['statusFilter', 'search', 'sortBy', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function setSortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function confirmReservation($reservationId)
    {
        $reservation = EquipmentReservation::findOrFail($reservationId);

        // Check permissions
        if (!Gate::allows('equipment.confirm-reservation')) {
            $this->dispatch('notify', type: 'error', message: 'Nie masz uprawnień do potwierdzania rezerwacji');
            return;
        }

        if ($reservation->status !== 'pending') {
            $this->dispatch('notify', type: 'error', message: 'Tylko rezerwacje oczekujące mogą być potwierdzone');
            return;
        }

        $reservation->update([
            'status' => 'confirmed',
            'confirmed_by_user_id' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        if ($reservation->equipment_set_id && $reservation->equipmentSet) {
            foreach ($reservation->equipmentSet->equipments as $equipment) {
                $equipment->update(['status' => 'reserved']);
            }
        } elseif ($reservation->equipment) {
            $this->updateEquipmentStatus($reservation->equipment);
        }

        $this->dispatch('notify', type: 'success', message: 'Rezerwacja została potwierdzona');
    }

    public function checkoutReservation($reservationId)
    {
        try {
            $reservation = EquipmentReservation::findOrFail($reservationId);

            // Check permissions
            if (!Gate::allows('rentals.create')) {
                $this->dispatch('notify', type: 'error', message: 'Nie masz uprawnień do wypożyczania sprzętu');
                return;
            }

            // Check if reservation is confirmed or pending
            if (!in_array($reservation->status, ['pending', 'confirmed'])) {
                $this->dispatch('notify', type: 'error', message: 'Tylko potwierdzone lub oczekujące rezerwacje mogą być wypożyczone');
                return;
            }

            // Check availability
            if ($reservation->equipment && !in_array($reservation->equipment->status, ['available', 'reserved'])) {
                $this->dispatch('notify', type: 'error', message: 'Sprzęt nie jest obecnie dostępny');
                return;
            }
            if ($reservation->equipment_set_id && $reservation->equipmentSet) {
                foreach ($reservation->equipmentSet->equipments as $equipment) {
                    if (!in_array($equipment->status, ['available', 'reserved'])) {
                        $this->dispatch('notify', type: 'error', message: 'Zestaw zawiera element niedostępny: ' . $equipment->name);
                        return;
                    }
                }
            }

            // Create rental group (generate name from user)
            $rentalGroup = \App\Models\RentalGroup::create([
                'name' => \App\Models\RentalGroup::generateName([$reservation->user_id]),
            ]);

            // Add user to rental group
            $rentalGroup->users()->attach($reservation->user_id);

            // Create rental
            $rental = \App\Models\Rental::create([
                'rental_group_id' => $rentalGroup->id,
                'equipment_id' => $reservation->equipment_id,
                'equipment_set_id' => $reservation->equipment_set_id,
                'rented_at' => now(),
                'rented_by_user_id' => Auth::id(),
                'rental_notes' => $reservation->reason ?? 'Wypożyczenie z rezerwacji',
            ]);

            // Update status
            if ($reservation->equipment) {
                $reservation->equipment->update(['status' => 'rented']);
            }

            if ($reservation->equipment_set_id && $reservation->equipmentSet) {
                foreach ($reservation->equipmentSet->equipments as $equipment) {
                    $equipment->update(['status' => 'rented']);
                }
            }

            // Update reservation
            $reservation->update([
                'status' => 'used',
                'actual_checkout_at' => now(),
            ]);

            if ($reservation->equipment) {
                $this->updateEquipmentStatus($reservation->equipment);
            }
            if ($reservation->equipment_set_id && $reservation->equipmentSet) {
                foreach ($reservation->equipmentSet->equipments as $equipment) {
                    $this->updateEquipmentStatus($equipment);
                }
            }

            $this->dispatch('notify', type: 'success', message: 'Sprzęt został wypożyczony');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Błąd: ' . $e->getMessage());
        }
    }

    public function cancelReservation($reservationId)
    {
        $reservation = EquipmentReservation::findOrFail($reservationId);

        // Check if user owns this reservation or is admin
        if ($reservation->user_id !== Auth::id() && !Gate::allows('equipment.cancel-any-reservation')) {
            $this->dispatch('notify', type: 'error', message: 'Nie masz uprawnień do anulowania tej rezerwacji');
            return;
        }

        // Only allow cancellation if reservation hasn't started yet
        if ($reservation->reserved_from <= now()) {
            $this->dispatch('notify', type: 'error', message: 'Nie można anulować rezerwacji, która już się rozpoczęła');
            return;
        }

        $reservation->update(['status' => 'cancelled']);

        if ($reservation->equipment) {
            $this->updateEquipmentStatus($reservation->equipment);
        }

        if ($reservation->equipment_set_id && $reservation->equipmentSet) {
            foreach ($reservation->equipmentSet->equipments as $equipment) {
                $this->updateEquipmentStatus($equipment);
            }
        }
        $this->dispatch('notify', type: 'success', message: 'Rezerwacja została anulowana');
    }

    private function updateEquipmentStatus(Equipment $equipment)
    {
        // Direct rentals
        $hasActiveRental = $equipment->rentals()->whereNull('returned_at')->exists();
        // Rentals via set containing this equipment
        $setIds = $equipment->equipmentSets()->pluck('equipment_sets.id');
        if ($setIds->isNotEmpty()) {
            $hasActiveRental = $hasActiveRental || \App\Models\Rental::whereIn('equipment_set_id', $setIds)->whereNull('returned_at')->exists();
        }
        if ($hasActiveRental) {
            $equipment->update(['status' => 'rented']);
            return;
        }

        $hasPendingReservation = EquipmentReservation::where('equipment_id', $equipment->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if (!$hasPendingReservation && $setIds->isNotEmpty()) {
            $hasPendingReservation = EquipmentReservation::whereIn('equipment_set_id', $setIds)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();
        }

        if ($hasPendingReservation) {
            $equipment->update(['status' => 'reserved']);
        } else {
            $equipment->update(['status' => 'available']);
        }
    }

    public function render()
    {
        $query = EquipmentReservation::with(['equipment', 'equipmentSet', 'user', 'group', 'confirmedBy'])
            ->where(function($q) {
                // Show user's own reservations or all if user is admin
                if (!Gate::allows('equipment.view-all-reservations')) {
                    $q->where('user_id', Auth::id());
                }
            })
            ->when($this->search, function($query) {
                $query->where(function($sub) {
                    $sub->whereHas('equipment', function($q) {
                        $q->where('name', 'LIKE', "%{$this->search}%")
                          ->orWhere('barcode', 'LIKE', "%{$this->search}%");
                    })
                    ->orWhereHas('equipmentSet', function($q) {
                        $q->where('name', 'LIKE', "%{$this->search}%")
                          ->orWhere('barcode', 'LIKE', "%{$this->search}%");
                    })
                    ->orWhereHas('user', function($q) {
                        $q->where('name', 'LIKE', "%{$this->search}%");
                    });
                });
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.my-reservations', [
            'reservations' => $query,
        ]);
    }
}
