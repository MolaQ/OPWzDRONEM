<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class InstructorDetail extends Component
{
    public int $instructorId;
    public string $name = '';
    public string $email = '';
    public array $roles = [];
    public string $createdAt = '';
    public array $supervisedGroups = [];
    public array $instructedGroups = [];

    public function mount($id)
    {
        /** @var \App\Models\User|null $user */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->can('users.view')) {
            abort(403, 'Brak uprawnień.');
        }

        $instructor = User::findOrFail($id);
        
        // Przechowuj tylko skalarne wartości
        $this->instructorId = $instructor->id;
        $this->name = $instructor->name;
        $this->email = $instructor->email;
        $this->roles = $instructor->getRoleNames()->toArray();
        $this->createdAt = $instructor->created_at->format('d.m.Y H:i');
        
        // Grupy, w których pełni funkcję wychowawcy
        $this->supervisedGroups = $instructor->supervisedGroups()
            ->get()
            ->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->name,
                'studentCount' => $group->users()->count(),
            ])
            ->toArray();
        
        // Grupy, w których pełni funkcję instruktora
        $this->instructedGroups = $instructor->instructedGroups()
            ->get()
            ->map(fn($group) => [
                'id' => $group->id,
                'name' => $group->name,
                'studentCount' => $group->users()->count(),
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.instructor-detail');
    }
}
