<?php

namespace App\Livewire\Admin;

use App\Models\Equipment;
use App\Models\EquipmentSet;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app.sidebar')]
class QrCodes extends Component
{
    public string $type = 'equipment';
    public string $search = '';
    public int $size = 180;
    public bool $showName = true;
    public bool $showSubtitle = true;
    public bool $showBarcode = true;

    protected $queryString = ['type', 'search'];

    public function updatedSize()
    {
        $this->size = max(120, min($this->size, 260));
    }

    public function render()
    {
        $items = $this->buildItems();

        return view('livewire.admin.qr-codes', [
            'items' => $items,
            'count' => $items->count(),
        ]);
    }

    private function buildItems()
    {
        $search = trim($this->search);

        if ($this->type === 'equipment_sets') {
            $query = EquipmentSet::query()
                ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%"))
                ->orderBy('name')
                ->get(['id', 'name', 'barcode', 'description']);

            return $query->map(function ($set) {
                return [
                    'id' => $set->id,
                    'name' => $set->name,
                    'subtitle' => $set->description,
                    'barcode' => $set->barcode,
                ];
            });
        }

        if ($this->type === 'students') {
            $query = User::role('student')
                ->with('group')
                ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%"))
                ->orderBy('name')
                ->get(['id', 'name', 'barcode', 'group_id']);

            return $query->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'subtitle' => $user->group->name ?? 'Uczeń',
                    'barcode' => $user->barcode,
                ];
            });
        }

        // Default: equipment
        $query = Equipment::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get(['id', 'name', 'barcode', 'model', 'category']);

        return $query->map(function ($equipment) {
            $subtitleParts = array_filter([$equipment->model, $equipment->category]);
            return [
                'id' => $equipment->id,
                'name' => $equipment->name,
                'subtitle' => implode(' • ', $subtitleParts),
                'barcode' => $equipment->barcode,
            ];
        });
    }
}
