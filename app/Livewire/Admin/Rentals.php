<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Rental;
use App\Models\RentalGroup;
use App\Models\User;
use App\Models\Equipment;
use App\Models\EquipmentSet;
use App\Services\BarcodeResolver;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.sidebar')]
class Rentals extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'active'; // active, all, overdue

    // Modal states
    public bool $showNewRentalModal = false;
    public string $studentSearch = '';
    public array $selectedStudents = [];
    public string $equipmentSearch = '';
    public array $selectedEquipment = [];

    // Rental form
    public string $rentalNotes = '';
    public array $availableEquipment = [];
    public array $searchResults = [];

    // Barcode scanning
    public string $barcodeInput = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function updatedStudentSearch()
    {
        if (strlen($this->studentSearch) >= 2) {
            $this->searchResults = User::where('active', true)
                ->role('student')
                ->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->studentSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->studentSearch . '%')
                        ->orWhere('barcode', 'like', '%' . $this->studentSearch . '%');
                })
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function updatedEquipmentSearch()
    {
        if (strlen($this->equipmentSearch) >= 2) {
            $this->availableEquipment = Equipment::where('status', 'available')
                ->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->equipmentSearch . '%')
                        ->orWhere('barcode', 'like', '%' . $this->equipmentSearch . '%');
                })
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->availableEquipment = [];
        }
    }

    public function addStudent($userId)
    {
        if (!in_array($userId, $this->selectedStudents)) {
            $this->selectedStudents[] = $userId;
        }
        $this->studentSearch = '';
        $this->searchResults = [];
    }

    public function removeStudent($userId)
    {
        $this->selectedStudents = array_filter($this->selectedStudents, function ($id) use ($userId) {
            return $id !== $userId;
        });
    }

    public function addEquipment($equipmentId)
    {
        if (!in_array($equipmentId, $this->selectedEquipment)) {
            $this->selectedEquipment[] = $equipmentId;
        }
        $this->equipmentSearch = '';
        $this->availableEquipment = [];
    }

    public function removeEquipment($equipmentId)
    {
        $this->selectedEquipment = array_filter($this->selectedEquipment, function ($id) use ($equipmentId) {
            return $id !== $equipmentId;
        });
    }

    public function openNewRentalModal()
    {
        $this->showNewRentalModal = true;
        $this->selectedStudents = [];
        $this->selectedEquipment = [];
        $this->studentSearch = '';
        $this->equipmentSearch = '';
        $this->rentalNotes = '';
    }

    public function closeNewRentalModal()
    {
        $this->showNewRentalModal = false;
    }

    public function createRental()
    {
        if (empty($this->selectedStudents) || empty($this->selectedEquipment)) {
            session()->flash('error', 'Wybierz uczniów i sprzęt');
            return;
        }

        try {
            DB::beginTransaction();

            // Tworz grupę wypożyczenia
            $rentalGroup = RentalGroup::create([
                'name' => 'Rental ' . now()->timestamp,
            ]);

            // Dodaj uczniów do grupy
            $rentalGroup->users()->attach($this->selectedStudents);

            // Dodaj wypożyczenia
            /** @var \App\Models\User|null $user */
            $user = auth()->guard('web')->user();
            foreach ($this->selectedEquipment as $equipmentId) {
                Rental::create([
                    'rental_group_id' => $rentalGroup->id,
                    'equipment_id' => $equipmentId,
                    'rented_at' => now(),
                    'rented_by_user_id' => $user?->id,
                    'rental_notes' => $this->rentalNotes,
                ]);

                // Zmień status na wypożyczony
                Equipment::find($equipmentId)->update(['status' => 'rented']);
            }

            DB::commit();
            session()->flash('success', 'Sprzęt został wypożyczony');
            $this->closeNewRentalModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Błąd podczas wypożyczania: ' . $e->getMessage());
        }
    }

    public function returnEquipment($rentalId)
    {
        try {
            $rental = Rental::findOrFail($rentalId);

            if ($rental->returned_at) {
                session()->flash('error', 'To wypożyczenie zostało już zwrócone');
                return;
            }

            DB::beginTransaction();

            $user = auth()->guard('web')->user();
            $rental->update([
                'returned_at' => now(),
                'returned_by_user_id' => $user?->id,
            ]);

            // Jeśli to było ostatnie wypożyczenie ze zestawu, zmień status
            if ($rental->equipment_id) {
                $otherRentals = Rental::where('equipment_id', $rental->equipment_id)
                    ->whereNull('returned_at')
                    ->exists();

                if (!$otherRentals) {
                    Equipment::find($rental->equipment_id)->update(['status' => 'available']);
                }
            }

            // Sprawdź czy cała grupa została zwrócona
            $activeRentals = $rental->rentalGroup->rentals()->whereNull('returned_at')->exists();
            if (!$activeRentals) {
                $rental->rentalGroup->update(['returned_at' => now()]);
            }

            DB::commit();
            session()->flash('success', 'Sprzęt został zwrócony');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Błąd podczas zwrotu: ' . $e->getMessage());
        }
    }

    public function forceReturn($rentalId)
    {
        $this->returnEquipment($rentalId);
    }

    public function handleBarcode(string $barcode = '')
    {
        $barcode = $barcode ?: $this->barcodeInput;

        if (empty($barcode)) {
            return;
        }

        $resolver = new BarcodeResolver();
        $result = $resolver->resolve($barcode);

        if (!$result['success']) {
            session()->flash('error', 'Nieznany kod kreskowy');
            $this->barcodeInput = '';
            return;
        }

        $entity = $result['entity'];
        $type = $result['type'];

        try {
            if ($type === 'student') {
                // Add student to selection
                if (!in_array($entity->id, $this->selectedStudents)) {
                    $this->selectedStudents[] = $entity->id;
                    session()->flash('success', "Dodany uczeń: {$entity->name}");
                } else {
                    session()->flash('info', "Uczeń {$entity->name} już dodany");
                }
            } elseif ($type === 'equipment') {
                // Add equipment to selection
                if ($entity->status !== 'available') {
                    session()->flash('error', "Sprzęt '{$entity->name}' nie jest dostępny ({$entity->status})");
                } elseif (!in_array($entity->id, $this->selectedEquipment)) {
                    $this->selectedEquipment[] = $entity->id;
                    session()->flash('success', "Dodany sprzęt: {$entity->name}");
                } else {
                    session()->flash('info', "Sprzęt {$entity->name} już dodany");
                }
            } elseif ($type === 'equipment_set') {
                // Add all equipment from set
                $setEquipment = $entity->equipments->where('status', 'available')->pluck('id')->toArray();

                if (empty($setEquipment)) {
                    session()->flash('error', "Zestaw '{$entity->name}' nie ma dostępnego sprzętu");
                } else {
                    $added = 0;
                    foreach ($setEquipment as $equipId) {
                        if (!in_array($equipId, $this->selectedEquipment)) {
                            $this->selectedEquipment[] = $equipId;
                            $added++;
                        }
                    }

                    if ($added > 0) {
                        session()->flash('success', "Dodany zestaw: {$entity->name} ($added przedmiotów)");
                    } else {
                        session()->flash('info', "Wszystkie przedmioty z zestawu {$entity->name} już dodane");
                    }
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', "Błąd przy dodawaniu: {$e->getMessage()}");
        }

        $this->barcodeInput = '';
    }

    public function updatedBarcodeInput($value)
    {
        // Auto-process when barcode is scanned (typically ends with Enter)
        // This is handled by wire:keydown.enter in the template
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
