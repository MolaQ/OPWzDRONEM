<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\BarcodeResolver;
use App\Services\RentalService;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class Returns extends Component
{
    public string $barcode = '';
    public ?array $result = null;
    public ?string $error = null;
    public array $suggestions = [];
    public bool $showSuggestions = false;

    // Return details
    public ?array $activeRental = null;
    public array $studentRentals = []; // List of student's active rentals
    public string $returnNotes = '';
    public string $noteType = 'info'; // info, warning, damage, maintenance

    protected BarcodeResolver $resolver;
    protected RentalService $rentalService;

    public function boot(BarcodeResolver $resolver, RentalService $rentalService)
    {
        $this->resolver = $resolver;
        $this->rentalService = $rentalService;
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

            if (!$result) {
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

    public function processReturn()
    {
        if (!$this->activeRental) {
            $this->error = 'Nie wybrano wypożyczenia do zwrotu';
            return;
        }

        try {
            $this->rentalService->returnItem(
                $this->activeRental['barcode'],
                Auth::id(),
                $this->returnNotes,
                $this->noteType
            );

            session()->flash('success', 'Zwrot został zarejestrowany pomyślnie');

            // Reset form
            $this->reset(['barcode', 'activeRental', 'studentRentals', 'returnNotes', 'noteType', 'result', 'error']);

        } catch (\Exception $e) {
            $this->error = 'Błąd podczas zwrotu: ' . $e->getMessage();
        }
    }

    public function resetForm()
    {
        $this->reset(['barcode', 'result', 'error', 'activeRental', 'studentRentals', 'returnNotes', 'noteType', 'suggestions', 'showSuggestions']);
    }

    public function render()
    {
        return view('livewire.admin.returns');
    }
}
