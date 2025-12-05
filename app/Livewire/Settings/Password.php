<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app.sidebar')]
class Password extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        /** @var User|null $user */
        $user = Auth::user();

        abort_unless($user instanceof User, Response::HTTP_UNAUTHORIZED);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}
