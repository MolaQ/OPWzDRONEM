<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class Permissions extends Component
{
    public $permissions;
    public $roles;
    public $permissionName = '';
    public $permissionDescription = '';
    public $selectedPermission = null;
    public $showModal = false;
    public $editMode = false;

    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->can('permissions.view')) {
            abort(403, 'Brak dostępu do zarządzania uprawnieniami');
        }
    }

    public function render()
    {
        $this->permissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0] ?? 'other';
        });
        $this->roles = Role::with('permissions')->get();

        return view('livewire.admin.permissions');
    }

    public function createPermission()
    {
        $this->authorize('permissions.create');

        $this->validate([
            'permissionName' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $this->permissionName]);

        $this->reset(['permissionName', 'permissionDescription', 'showModal']);
        $this->dispatch('permission-created');
    }

    public function editPermission($permissionId)
    {
        $this->authorize('permissions.edit');

        $permission = Permission::findOrFail($permissionId);
        $this->selectedPermission = $permissionId;
        $this->permissionName = $permission->name;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function updatePermission()
    {
        $this->authorize('permissions.edit');

        $this->validate([
            'permissionName' => 'required|string|max:255|unique:permissions,name,' . $this->selectedPermission,
        ]);

        $permission = Permission::findOrFail($this->selectedPermission);
        $permission->update(['name' => $this->permissionName]);

        $this->reset(['selectedPermission', 'permissionName', 'showModal', 'editMode']);
        $this->dispatch('permission-updated');
    }

    public function deletePermission($permissionId)
    {
        $this->authorize('permissions.delete');

        $permission = Permission::findOrFail($permissionId);
        $permission->delete();

        $this->dispatch('permission-deleted');
    }

    public function openModal()
    {
        $this->reset(['selectedPermission', 'permissionName', 'editMode']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['selectedPermission', 'permissionName', 'showModal', 'editMode']);
    }
}
