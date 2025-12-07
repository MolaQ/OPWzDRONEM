<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class Roles extends Component
{
    public $roles;
    public $permissions;
    public $selectedRole = null;
    public $roleName = '';
    public $rolePermissions = [];
    public $showModal = false;
    public $editMode = false;

    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->can('roles.view')) {
            abort(403, 'Brak dostępu do zarządzania rolami');
        }
    }

    public function render()
    {
        $this->roles = Role::with('permissions')->get();
        $this->permissions = Permission::all();

        return view('livewire.admin.roles');
    }

    public function createRole()
    {
        $this->authorize('roles.create');

        $this->validate([
            'roleName' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create(['name' => $this->roleName]);
        $role->syncPermissions($this->rolePermissions);

        $this->reset(['roleName', 'rolePermissions', 'showModal']);
        $this->dispatch('role-created');
    }

    public function editRole($roleId)
    {
        $this->authorize('roles.edit');

        $role = Role::findOrFail($roleId);
        $this->selectedRole = $roleId;
        $this->roleName = $role->name;
        $this->rolePermissions = $role->permissions->pluck('name')->toArray();
        $this->editMode = true;
        $this->showModal = true;
    }

    public function updateRole()
    {
        $this->authorize('roles.edit');

        $this->validate([
            'roleName' => 'required|string|max:255|unique:roles,name,' . $this->selectedRole,
        ]);

        $role = Role::findOrFail($this->selectedRole);
        $role->update(['name' => $this->roleName]);
        $role->syncPermissions($this->rolePermissions);

        $this->reset(['selectedRole', 'roleName', 'rolePermissions', 'showModal', 'editMode']);
        $this->dispatch('role-updated');
    }

    public function deleteRole($roleId)
    {
        $this->authorize('roles.delete');

        $role = Role::findOrFail($roleId);

        // Nie pozwól usunąć systemowych ról
        if (in_array($role->name, ['admin', 'student', 'instruktor', 'wychowawca', 'koordynator'])) {
            $this->dispatch('role-delete-error', message: 'Nie można usunąć systemowej roli');
            return;
        }

        $role->delete();
        $this->dispatch('role-deleted');
    }

    public function openModal()
    {
        $this->reset(['selectedRole', 'roleName', 'rolePermissions', 'editMode']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['selectedRole', 'roleName', 'rolePermissions', 'showModal', 'editMode']);
    }
}
