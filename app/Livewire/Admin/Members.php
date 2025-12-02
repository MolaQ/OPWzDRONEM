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
        'role' => 'student',
        'group_id' => null,
        'active' => true,
        'password' => '',
    ];

    // Livewire 3 uses Tailwind pagination by default

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
            'role' => 'student',
            'group_id' => null,
            'active' => true,
            'password' => '',
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
            'role' => $user->roles->first()?->name ?? 'student',
            'group_id' => $user->group_id,
            'active' => $user->active,
            'password' => '',
        ];
        $this->showModal = true;
    }

    public function saveUser()
    {
        $rules = [
            'editingUser.name' => 'required|string|max:255',
            'editingUser.email' => 'required|email|unique:users,email,' . ($this->editingUser['id'] ?? 'NULL'),
            'editingUser.role' => 'required|string|in:student,admin,instructor,guest',
            'editingUser.group_id' => 'nullable|exists:groups,id',
            'editingUser.active' => 'required|boolean',
        ];

        // Hasło wymagane tylko przy tworzeniu nowego użytkownika
        if (!$this->editingUser['id']) {
            $rules['editingUser.password'] = 'required|string|min:8';
        } else {
            // Przy edycji hasło jest opcjonalne
            $rules['editingUser.password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        if ($this->editingUser['id']) {
            $user = User::find($this->editingUser['id']);
            $data = $this->editingUser;
            $roleName = $data['role'];
            unset($data['role']);

            // Jeśli hasło jest puste przy edycji, usuń je z danych
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = bcrypt($data['password']);
            }

            $user->update($data);
            $user->syncRoles([$roleName]);
            $msg = 'Zaktualizowano dane użytkownika!';
            $type = 'success';
        } else {
            $data = $this->editingUser;
            $roleName = $data['role'];
            unset($data['role']);
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);
            $user->assignRole($roleName);
            $msg = 'Dodano użytkownika!';
            $type = 'success';
        }

        $this->dispatch('notify', type: $type, message: $msg);
        $this->showModal = false;
        $this->editingUser = [
            'id' => null,
            'name' => '',
            'email' => '',
            'role' => 'student',
            'group_id' => null,
            'active' => true,
            'password' => '',
        ];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingUser = [
            'id' => null,
            'name' => '',
            'email' => '',
            'role' => 'student',
            'group_id' => null,
            'active' => true,
            'password' => '',
        ];
        $this->resetValidation();
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Użytkownik został usunięty!');
    }

    public function render()
    {
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name')->toArray();
        $groups = Group::select('id', 'name')->orderBy('name')->get();

        $users = User::with(['group', 'roles'])
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhereHas('group', function($groupQuery) {
                          $groupQuery->where('name', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->role, function($q) {
                $q->whereHas('roles', function($roleQuery) {
                    $roleQuery->where('name', $this->role);
                });
            })
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
