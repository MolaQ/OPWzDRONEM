<?php

namespace App\Http\Middleware;

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

        // Sprawdzenie czy użytkownik ma uprawnienia do panelu administracyjnego
        if (!$user->can('access admin panel')) {
            return Redirect::route('home')->with('error', 'Brak autoryzacji');
        }

        // Admin middleware nie weryfikuje licencji ani aktywności grupy.
        // Użytkownicy bez uprawnień i tak nie przejdą powyższego warunku.

        // Jeśli wszystkie warunki spełnione, kontynuuj obsługę żądania
        return $next($request);
    }
}

