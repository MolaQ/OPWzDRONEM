<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class GroupDetail extends Component
{
    public int $groupId;
    public string $groupName = '';
    public string $groupDescription = '';
    public bool $groupActive = true;
    public array $students = [];
    public array $supervisors = [];
    public array $instructors = [];

    public function mount($id)
    {
        /** @var \App\Models\User|null $user */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->can('users.view')) {
            abort(403, 'Brak uprawnień.');
        }

        $group = Group::findOrFail($id);

        $this->groupId = $group->id;
        $this->groupName = $group->name;
        $this->groupDescription = $group->description ?? '';
        $this->groupActive = $group->active;

        // Pobierz studentów grupy
        $this->students = $group->users()
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'barcode' => $user->barcode ?? '',
            ])
            ->toArray();

        // Pobierz wychowawców grupy
        $this->supervisors = $group->supervisors()
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->toArray();

        // Pobierz instruktorów grupy
        $this->instructors = $group->instructors()
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.group-detail');
    }
}
