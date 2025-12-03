<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Rental;
use App\Models\RentalGroup;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.sidebar')]
class Rentals extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'active'; // active, all, overdue

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function forceReturn($rentalId)
    {
        $rental = Rental::findOrFail($rentalId);

        if ($rental->returned_at) {
            session()->flash('error', 'To wypożyczenie zostało już zwrócone');
            return;
        }

        $rental->update([
            'returned_at' => now(),
            'returned_by_user_id' => auth()->id(),
            'return_notes' => 'Zwrot wymuszony przez administratora'
        ]);

        session()->flash('success', 'Wypożyczenie zostało zwrócone');
    }

    public function render()
    {
        $query = RentalGroup::with(['rentals.equipment', 'rentals.equipmentSet', 'rentedByUser', 'users'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->filter === 'active') {
            $query->whereHas('rentals', function ($q) {
                $q->whereNull('returned_at');
            });
        }

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('users', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('rentals.equipment', function ($equipQuery) {
                    $equipQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('rentals.equipmentSet', function ($setQuery) {
                    $setQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            });
        }

        $rentalGroups = $query->paginate(20);

        // Totals for quick feedback
        $totalGroups = $rentalGroups->total();
        $totalItems = collect($rentalGroups->items())
            ->reduce(function ($carry, $group) {
                return $carry + ($group->rentals?->count() ?? 0);
            }, 0);

        return view('livewire.admin.rentals', [
            'rentalGroups' => $rentalGroups,
            'totalGroups' => $totalGroups,
            'totalItems' => $totalItems,
        ]);
    }
}
