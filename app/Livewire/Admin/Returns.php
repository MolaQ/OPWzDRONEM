<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Services\BarcodeResolver;
use App\Models\Rental;
use App\Models\RentalGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\EquipmentNote;

#[Layout('components.layouts.app.sidebar')]
class Returns extends Component
{
    use WithPagination;

    public string $search = '';
    public string $barcode = '';
    public ?array $result = null;
    public ?string $error = null;
    public array $suggestions = [];
    public bool $showSuggestions = false;

    // Return details
    public ?array $activeRental = null;
    public array $studentRentals = [];
    public string $returnNotes = '';
    public string $noteType = 'info';

    // Return items and statuses
    public array $returnItems = [];
    public array $itemStatuses = [];
    public array $itemSelected = [];

    public array $statusOptions = [
        'available' => 'Dostępny',
        'maintenance' => 'Konserwacja (ładowanie)',
        'damaged' => 'Uszkodzony',
        'in_repair' => 'W naprawie',
        'retired' => 'Wycofany',
    ];

    // Modal states
    public bool $showReturnModal = false;
    public ?int $selectedRentalId = null;

    protected BarcodeResolver $resolver;

    public function boot(BarcodeResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedBarcode()
    {
        $this->error = null;
        $this->result = null;

        if (strlen($this->barcode) >= 2) {
            $this->suggestions = $this->resolver->getSuggestions($this->barcode, 'all');
            $this->showSuggestions = count($this->suggestions) > 0;
        } else {
            $this->suggestions = [];
            $this->showSuggestions = false;
        }
    }

    public function selectSuggestion(string $barcode, string $type, int $id, string $name)
    {
        $this->barcode = $barcode;
        $this->suggestions = [];
        $this->showSuggestions = false;

        $this->scanForReturn();
    }

    public function scanForReturn()
    {
        $this->error = null;
        $this->result = null;
        $this->activeRental = null;
        $this->studentRentals = [];

        if (empty($this->barcode)) {
            $this->error = 'Wprowadź kod kreskowy';
            return;
        }

        try {
            $result = $this->resolver->resolve($this->barcode);

            if (!$result['found']) {
                $this->error = 'Nie rozpoznano kodu kreskowego';
                return;
            }

            $this->result = $result;

            // If it's a student, show their active rentals
            if ($result['type'] === 'student') {
                $this->loadStudentRentals($result['id']);
                return;
            }

            // If it's equipment or set, find and show the rental
            $this->loadRentalDetails($result['type'], $result['id']);

        } catch (\Exception $e) {
            $this->error = 'Błąd: ' . $e->getMessage();
        }
    }

    private function loadStudentRentals(int $userId)
    {
        $rentals = Rental::whereHas('rentalGroup.users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })
        ->whereNull('returned_at')
        ->with(['equipment', 'equipmentSet', 'rentalGroup.users'])
        ->get();

        if ($rentals->isEmpty()) {
            $this->error = 'Ten uczeń nie ma aktywnych wypożyczeń';
            return;
        }

        $this->studentRentals = $rentals->map(function ($rental) {
            $item = $rental->equipment ?? $rental->equipmentSet;
            return [
                'id' => $rental->id,
                'type' => $rental->equipment ? 'equipment' : 'equipment_set',
                'barcode' => $item?->barcode,
                'name' => $item?->name,
                'rented_at' => $rental->rented_at->format('Y-m-d H:i'),
                'students' => $rental->rentalGroup->users->pluck('name')->join(', '),
                'notes' => $rental->rental_notes,
            ];
        })->toArray();
    }

    private function loadRentalDetails(string $type, int $id)
    {
        $rental = null;

        if ($type === 'equipment') {
            $rental = Rental::where('equipment_id', $id)
                ->whereNull('returned_at')
                ->with(['equipment', 'rentalGroup.users'])
                ->first();
        } elseif ($type === 'equipment_set') {
            $rental = Rental::where('equipment_set_id', $id)
                ->whereNull('returned_at')
                ->with(['equipmentSet', 'rentalGroup.users'])
                ->first();
        }

        if (!$rental) {
            $this->error = 'Nie znaleziono aktywnego wypożyczenia dla tego przedmiotu';
            return;
        }

        $item = $rental->equipment ?? $rental->equipmentSet;

        $this->activeRental = [
            'id' => $rental->id,
            'type' => $rental->equipment ? 'equipment' : 'equipment_set',
            'barcode' => $item?->barcode,
            'name' => $item?->name,
            'rented_at' => $rental->rented_at->format('Y-m-d H:i'),
            'students' => $rental->rentalGroup->users->pluck('name')->join(', '),
            'notes' => $rental->rental_notes,
        ];
    }

    public function selectRentalItem(string $barcode)
    {
        $this->barcode = $barcode;
        $this->studentRentals = [];
        $this->scanForReturn();
    }

    public function openReturnModal(int $rentalId)
    {
        $this->selectedRentalId = $rentalId;
        $this->showReturnModal = true;
        $this->returnNotes = '';
        $this->noteType = 'info';

        $this->loadReturnItems($rentalId);
    }

    public function closeReturnModal()
    {
        $this->showReturnModal = false;
        $this->selectedRentalId = null;
    }

    public function processReturn()
    {
        if (!$this->selectedRentalId) {
            $this->error = 'Nie wybrano wypożyczenia do zwrotu';
            return;
        }

        $selectedCount = collect($this->itemSelected)->filter(fn($v) => (bool)$v)->count();
        if ($selectedCount === 0) {
            $this->error = 'Zaznacz przynajmniej jeden element do zwrotu';
            return;
        }

        try {
            $rental = Rental::findOrFail($this->selectedRentalId);

            if ($rental->returned_at) {
                session()->flash('error', 'To wypożyczenie zostało już zwrócone');
                $this->closeReturnModal();
                return;
            }

            DB::beginTransaction();

            /** @var \App\Models\User|null $user */
            $user = auth()->guard('web')->user();
            $rental->update([
                'returned_at' => now(),
                'returned_by_user_id' => $user?->id,
                'return_notes' => $this->returnNotes,
            ]);

            // Apply status updates for equipment or entire set
            if ($rental->equipment_id) {
                $status = $this->itemStatuses[$rental->equipment_id] ?? 'available';
                $this->updateEquipmentStatus($rental->equipment, $status, $rental);
            }

            if ($rental->equipment_set_id) {
                $equipments = $rental->equipmentSet->equipments;
                foreach ($equipments as $equipment) {
                    // Skip unchecked items if user deselected
                    if (isset($this->itemSelected[$equipment->id]) && !$this->itemSelected[$equipment->id]) {
                        continue;
                    }

                    $status = $this->itemStatuses[$equipment->id] ?? $this->defaultStatus($equipment);
                    $this->updateEquipmentStatus($equipment, $status, $rental);
                }
            }

            // Check if entire group was returned
            $activeRentals = $rental->rentalGroup->rentals()->whereNull('returned_at')->exists();
            if (!$activeRentals) {
                $rental->rentalGroup->update(['returned_at' => now()]);
            }

            DB::commit();
            session()->flash('success', 'Sprzęt został zwrócony');
            $this->closeReturnModal();
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Błąd podczas zwrotu: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset(['barcode', 'result', 'error', 'activeRental', 'studentRentals', 'returnNotes', 'noteType', 'suggestions', 'showSuggestions', 'returnItems', 'itemStatuses', 'itemSelected']);
    }

    private function loadReturnItems(int $rentalId): void
    {
        $this->returnItems = [];
        $this->itemStatuses = [];
        $this->itemSelected = [];

        $rental = Rental::with(['equipment', 'equipmentSet.equipments'])->find($rentalId);
        if (!$rental) {
            return;
        }

        if ($rental->equipment) {
            $defaultStatus = $this->defaultStatus($rental->equipment);
            $this->returnItems[] = [
                'id' => $rental->equipment->id,
                'name' => $rental->equipment->name,
                'barcode' => $rental->equipment->barcode,
                'category' => $rental->equipment->category,
            ];
            $this->itemStatuses[$rental->equipment->id] = $defaultStatus;
            $this->itemSelected[$rental->equipment->id] = true;
        }

        if ($rental->equipmentSet) {
            foreach ($rental->equipmentSet->equipments as $equipment) {
                $defaultStatus = $this->defaultStatus($equipment);
                $this->returnItems[] = [
                    'id' => $equipment->id,
                    'name' => $equipment->name,
                    'barcode' => $equipment->barcode,
                    'category' => $equipment->category,
                ];
                $this->itemStatuses[$equipment->id] = $defaultStatus;
                $this->itemSelected[$equipment->id] = true;
            }
        }
    }

    private function defaultStatus($equipment): string
    {
        $text = strtolower(($equipment->category ?? '') . ' ' . ($equipment->name ?? ''));
        if (str_contains($text, 'bat') || str_contains($text, 'aku')) {
            return 'maintenance';
        }

        return 'available';
    }

    private function updateEquipmentStatus($equipment, string $status, Rental $rental): void
    {
        $allowed = array_keys($this->statusOptions);
        if (!in_array($status, $allowed)) {
            $status = 'available';
        }

        $equipment->update(['status' => $status]);

        if ($this->returnNotes || $this->noteType !== 'info') {
            EquipmentNote::create([
                'equipment_id' => $equipment->id,
                'rental_id' => $rental->id,
                'note' => $this->returnNotes ?: 'Aktualizacja statusu przy zwrocie',
                'type' => $this->noteType,
                'created_by_user_id' => Auth::id(),
            ]);
        }
    }

    public function render()
    {
        $query = RentalGroup::with(['rentals.equipment', 'rentals.equipmentSet', 'rentedByUser', 'users'])
            ->whereHas('rentals', function ($q) {
                $q->whereNull('returned_at');
            })
            ->orderBy('created_at', 'desc');

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

        return view('livewire.admin.returns', [
            'rentalGroups' => $rentalGroups,
        ]);
    }
}
