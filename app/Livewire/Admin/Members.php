<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Group;

class Members extends Component
{
    use WithPagination;

    public $search = '';
    public $role = '';
    public $group_id = '';
    public $active = '';
    public $showModal = false;
    public $editingUser = [
        'id' => null,
        'name' => '',
        'email' => '',
        'role' => 'user',
        'group_id' => null,
        'active' => true,
    ];

    protected $paginationTheme = 'bootstrap'; // możesz zmienić na 'tailwind' lub custom

    protected $updatesQueryString = [
        'search', 'role', 'group_id', 'active', 'page'
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingRole() { $this->resetPage(); }
    public function updatingGroupId() { $this->resetPage(); }
    public function updatingActive() { $this->resetPage(); }

    public function showCreateModal()
    {
        $this->editingUser = [
            'id' => null,
            'name' => '',
            'email' => '',
            'role' => 'user',
            'group_id' => null,
            'active' => true,
        ];
        $this->showModal = true;
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->editingUser = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'group_id' => $user->group_id,
            'active' => $user->active,
        ];
        $this->showModal = true;
    }

    public function saveUser()
    {
        $this->validate([
            'editingUser.name' => 'required|string|max:255',
            'editingUser.email' => 'required|email|unique:users,email,' . ($this->editingUser['id'] ?? 'NULL'),
            'editingUser.role' => 'required|string|in:user,admin,instructor',
            'editingUser.group_id' => 'nullable|exists:groups,id',
            'editingUser.active' => 'required|boolean',
        ]);

        if ($this->editingUser['id']) {
            $user = User::find($this->editingUser['id']);
            $user->update($this->editingUser);
            $msg = 'Zaktualizowano dane użytkownika!';
        } else {
            User::create($this->editingUser);
            $msg = 'Dodano użytkownika!';
        }

        session()->flash('message', $msg);
        $this->showModal = false;
        $this->editingUser = [
            'id' => null,
            'name' => '',
            'email' => '',
            'role' => 'user',
            'group_id' => null,
            'active' => true,
        ];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingUser = [
            'id' => null,
            'name' => '',
            'email' => '',
            'role' => 'user',
            'group_id' => null,
            'active' => true,
        ];
        $this->resetValidation();
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Użytkownik został usunięty!');
    }

    public function render()
    {
        $roles = User::select('role')->distinct()->orderBy('role')->pluck('role')->toArray();
        $groups = Group::select('id', 'name')->orderBy('name')->get();

        $users = User::with('group')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhereHas('group', function($groupQuery) {
                          $groupQuery->where('name', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->role, fn($q) => $q->where('role', $this->role))
            ->when($this->group_id, fn($q) => $q->where('group_id', $this->group_id))
            ->when($this->active !== '', fn($q) => $q->where('active', $this->active))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.members', [
            'users' => $users,
            'roles' => $roles,
            'groups' => $groups,
        ]);
    }
}
