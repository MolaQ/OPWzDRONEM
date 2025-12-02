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

    protected BarcodeResolver $resolver;

    public function boot(BarcodeResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function updatedBarcode()
    {
        $this->error = null;
        $this->result = null;

        if (empty($this->barcode)) {
            return;
        }

        $this->scan();
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
