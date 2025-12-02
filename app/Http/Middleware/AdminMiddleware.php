<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

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

        // Dla studentów sprawdzamy ważność licencji
        if ($user->hasRole('student')) {
            if (!$user->license_expiry_date) {
                return Redirect::route('home')->with('error', 'Brak dostępu - brak daty ważności licencji pilota');
            }
            if (Carbon::now()->greaterThan(Carbon::parse($user->license_expiry_date))) {
                return Redirect::route('home')->with('error', 'Brak dostępu - licencja pilota wygasła');
            }
        }

        // Sprawdzenie aktywności grupy, tylko jeśli użytkownik do grupy należy
        $group = $user->group;
        if ($group) {
            if (!$group->active) {
                return Redirect::route('home')->with('error', 'Brak dostępu - grupa użytkownika nieaktywna');
            }
        }

        // Jeśli wszystkie warunki spełnione, kontynuuj obsługę żądania
        return $next($request);
    }
}

