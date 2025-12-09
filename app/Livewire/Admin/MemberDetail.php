<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app.sidebar')]
class MemberDetail extends Component
{
    public User $member;

    public function mount($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->can('users.view')) {
            abort(403, 'Brak uprawnień.');
        }

        $this->member = User::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.member-detail', [
            'member' => $this->member,
        ]);
    }
}
