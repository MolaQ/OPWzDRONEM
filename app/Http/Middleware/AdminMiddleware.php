<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Sprawdzenie czy użytkownik jest zalogowany i aktywny
        if (!$user || !$user->active) {
            return Redirect::route('home')->with('error', 'Brak dostępu - konto nieaktywne');
        }

        if (!$user instanceof User) {
            return Redirect::route('home')->with('error', 'Brak autoryzacji');
        }

        // Sprawdzenie czy użytkownik ma uprawnienia do panelu administracyjnego
        if (!$user->hasPermissionTo('admin.panel.access')) {
            return Redirect::route('home')->with('error', 'Brak autoryzacji');
        }

        // Admin middleware nie weryfikuje licencji ani aktywności grupy.
        // Użytkownicy bez uprawnień i tak nie przejdą powyższego warunku.

        // Jeśli wszystkie warunki spełnione, kontynuuj obsługę żądania
        return $next($request);
    }
}

