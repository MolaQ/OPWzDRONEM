<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Group;

class Groups extends Component
{
    use WithPagination;

    public $search = '';
    public $active = '';
    public $showModal = false;
    public $editingGroup = [
        'id' => null,
        'name' => '',
        'description' => '',
        'active' => true,
    ];

    protected $paginationTheme = 'bootstrap';

    protected $updatesQueryString = [
        'search', 'active', 'page'
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingActive() { $this->resetPage(); }

    public function showCreateModal()
    {
        $this->editingGroup = [
            'id' => null,
            'name' => '',
            'description' => '',
            'active' => true,
        ];
        $this->showModal = true;
    }

    public function editGroup($id)
    {
        $group = Group::findOrFail($id);
        $this->editingGroup = [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description,
            'active' => $group->active,
        ];
        $this->showModal = true;
    }

    public function saveGroup()
    {
        $this->validate([
            'editingGroup.name' => 'required|string|max:255',
            'editingGroup.description' => 'nullable|string|max:500',
            'editingGroup.active' => 'required|boolean',
        ]);

        if ($this->editingGroup['id']) {
            $group = Group::find($this->editingGroup['id']);
            $group->update($this->editingGroup);
            $msg = 'Zaktualizowano dane grupy!';
        } else {
            Group::create($this->editingGroup);
            $msg = 'Dodano grupę!';
        }

        session()->flash('message', $msg);
        $this->showModal = false;
        $this->editingGroup = [
            'id' => null,
            'name' => '',
            'description' => '',
            'active' => true,
        ];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingGroup = [
            'id' => null,
            'name' => '',
            'description' => '',
            'active' => true,
        ];
        $this->resetValidation();
    }

    public function deleteGroup($id)
    {
        $group = Group::findOrFail($id);

        if ($group->users()->count() > 0) {
            session()->flash('error', 'Nie można usunąć grupy, która ma przypisanych użytkowników!');
            return;
        }

        $group->delete();
        session()->flash('message', 'Grupa została usunięta!');
    }

    public function render()
    {
        $groups = Group::withCount('users')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->active !== '', fn($q) => $q->where('active', $this->active))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.groups', [
            'groups' => $groups,
        ]);
    }
}
