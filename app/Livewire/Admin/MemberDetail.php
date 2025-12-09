<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class MemberDetail extends Component
{
    public int $memberId;
    public string $name = '';
    public string $email = '';
    public string $barcode = '';
    public ?string $groupName = null;
    public ?int $groupId = null;
    public bool $active = true;
    public array $roles = [];
    public string $createdAt = '';
    public array $supervisors = [];
    public array $instructors = [];
    public array $supervisedGroups = [];
    public array $instructedGroups = [];
    public bool $isStudent = false;
    public bool $isSupervisor = false;
    public bool $isInstructor = false;

    public function mount($id)
    {
        /** @var \App\Models\User|null $user */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->can('users.view')) {
            abort(403, 'Brak uprawnień.');
        }

        $member = User::findOrFail($id);

        // Przechowuj tylko skalarne wartości
        $this->memberId = $member->id;
        $this->name = $member->name;
        $this->email = $member->email;
        $this->barcode = $member->barcode ?? '';
        $this->active = $member->active;
        $this->roles = $member->getRoleNames()->toArray();
        $this->createdAt = $member->created_at->format('d.m.Y H:i');

        // Sprawdź role
        $this->isStudent = $member->hasRole('student');
        $this->isSupervisor = $member->supervisedGroups()->exists();
        $this->isInstructor = $member->instructedGroups()->exists();

        // Jeśli student - pobierz jego grupę
        if ($this->isStudent) {
            $this->groupName = $member->group?->name ?? null;
            $this->groupId = $member->group?->id ?? null;

            // Pobierz wychowawców i instruktorów grupy studenta
            if ($member->group) {
                $this->supervisors = $member->group->supervisors()
                    ->get()
                    ->map(fn($user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ])
                    ->toArray();

                $this->instructors = $member->group->instructors()
                    ->get()
                    ->map(fn($user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ])
                    ->toArray();
            }
        }

        // Jeśli wychowawca - pobierz grupy które nadzoruje
        if ($this->isSupervisor) {
            $this->supervisedGroups = $member->supervisedGroups()
                ->with('users')
                ->get()
                ->map(fn($group) => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'students' => $group->users()
                        ->get()
                        ->map(fn($user) => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'barcode' => $user->barcode ?? '',
                        ])
                        ->toArray(),
                ])
                ->toArray();
        }

        // Jeśli instruktor - pobierz grupy które prowadzi
        if ($this->isInstructor) {
            $this->instructedGroups = $member->instructedGroups()
                ->with('users')
                ->get()
                ->map(fn($group) => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'students' => $group->users()
                        ->get()
                        ->map(fn($user) => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'barcode' => $user->barcode ?? '',
                        ])
                        ->toArray(),
                ])
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.admin.member-detail');
    }
}
