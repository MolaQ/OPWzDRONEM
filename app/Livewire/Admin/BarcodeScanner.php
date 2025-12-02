<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\BarcodeResolver;

#[Layout('components.layouts.app.sidebar')]
class BarcodeScanner extends Component
{
    public string $barcode = '';
    public ?array $result = null;
    public ?string $error = null;
    public array $suggestions = [];
    public bool $showSuggestions = false;

    protected BarcodeResolver $resolver;

    public function boot(BarcodeResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function updatedBarcode()
    {
        $this->error = null;
        $this->result = null;

        $q = strtoupper(trim($this->barcode));

        if ($q === '') {
            $this->suggestions = [];
            $this->showSuggestions = false;
            return;
        }

        // If full valid barcode, perform scan; otherwise show suggestions
        if ($this->resolver->isValidFormat($q)) {
            $this->showSuggestions = false;
            $this->scan();
            return;
        }

        $this->suggestions = $this->buildSuggestions($q);
        $this->showSuggestions = !empty($this->suggestions);
    }

    public function scan()
    {
        $this->error = null;
        $this->result = null;

        if (empty($this->barcode)) {
            $this->error = 'Wprowadź kod kreskowy';
            return;
        }

        // Validate format
        if (!$this->resolver->isValidFormat($this->barcode)) {
            $this->error = 'Nieprawidłowy format kodu. Użyj formatu: S12345 (student) lub E12345 (sprzęt)';
            return;
        }

        // Resolve barcode
        $resolved = $this->resolver->resolve($this->barcode);

        if (!$resolved['found']) {
            $this->error = 'Nie znaleziono rekordu dla kodu: ' . $this->barcode;
            return;
        }

        $this->result = $resolved;

        // Clear barcode for next scan
        $this->barcode = '';

        // Dispatch event for focus
        $this->dispatch('scanned');
    }

    /**
     * Build typeahead suggestions based on partial query
     */
    protected function buildSuggestions(string $q): array
    {
        $suggestions = [];

        // If only prefix S/E provided
        if (in_array($q, [BarcodeResolver::PREFIX_STUDENT, BarcodeResolver::PREFIX_EQUIPMENT], true)) {
            if ($q === BarcodeResolver::PREFIX_STUDENT) {
                $users = \App\Models\User::where('barcode', 'LIKE', 'S%')
                    ->orderBy('name')
                    ->limit(20)
                    ->get(['id','name','barcode']);
                foreach ($users as $u) {
                    $suggestions[] = [
                        'type' => 'student',
                        'id' => $u->id,
                        'name' => $u->name,
                        'barcode' => $u->barcode,
                    ];
                }
            } else {
                $eqs = \App\Models\Equipment::where('barcode', 'LIKE', 'E%')
                    ->orderBy('name')
                    ->limit(20)
                    ->get(['id','name','barcode']);
                foreach ($eqs as $e) {
                    $suggestions[] = [
                        'type' => 'equipment',
                        'id' => $e->id,
                        'name' => $e->name,
                        'barcode' => $e->barcode,
                    ];
                }
            }
            return $suggestions;
        }

        // General substring search across users and equipment barcodes
        $users = \App\Models\User::where('barcode', 'LIKE', "%$q%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id','name','barcode']);
        foreach ($users as $u) {
            $suggestions[] = [
                'type' => 'student',
                'id' => $u->id,
                'name' => $u->name,
                'barcode' => $u->barcode,
            ];
        }

        $eqs = \App\Models\Equipment::where('barcode', 'LIKE', "%$q%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id','name','barcode']);
        foreach ($eqs as $e) {
            $suggestions[] = [
                'type' => 'equipment',
                'id' => $e->id,
                'name' => $e->name,
                'barcode' => $e->barcode,
            ];
        }

        return $suggestions;
    }

    /**
     * Select a suggestion and scan
     */
    public function selectSuggestion(string $barcode): void
    {
        $this->barcode = strtoupper($barcode);
        $this->showSuggestions = false;
        $this->scan();
    }

    public function clear()
    {
        $this->barcode = '';
        $this->result = null;
        $this->error = null;
    }

    public function render()
    {
        return view('livewire.admin.barcode-scanner');
    }
}
