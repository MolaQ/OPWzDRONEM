# Admin Roles & Permissions UI Plan

## 1. Cel i kontekst
- Przenieść zarządzanie rolami i uprawnieniami do osobnej sekcji panelu admina opartej na nowym systemie Spatie (zob. `database/seeders/RolesAndPermissionsSeeder.php`).
- Umożliwić administratorom i koordynatorom szybkie porównywanie ról, edycję przypisanych uprawnień oraz ręczne nadawanie ról/upr. konkretnym użytkownikom (komplementarne do istniejącego komponentu `App\Livewire\Admin\Members`).
- Zachować spójność z estetyką widoków osadzonych w layoucie `components.layouts.app.sidebar`.

## 2. Architektura widoków
1. **Roles Overview (`admin.roles.index`)**
   - Livewire komponent `App\Livewire\Admin\Roles\Index`.
   - Tabela ról z kolumnami: nazwa, liczba użytkowników, liczba uprawnień, ostatnia modyfikacja.
   - Pasek boczny / drawer z listą kategorii uprawnień (Panel, Users, Equipment, Content, Settings) umożliwiający szybkie filtrowanie.
   - Akcje: "Dodaj rolę", "Duplikuj", "Eksportuj" (CSV/JSON listy uprawnień).

2. **Role Detail & Permission Matrix (`admin.roles.show`)**
   - Komponent `App\Livewire\Admin\Roles\Show` renderowany w modalu/drawerze.
   - Sekcja nagłówkowa: nazwa, opis, przełączniki (rola systemowa - nie modyfikować krytycznych: `admin`, `koordynator`).
   - Widok typu matrix (permissions x kategorie) z możliwością zaznaczania/odznaczania oraz podglądaniem dziedziczenia (ikona kłódki dla uprawnień wymuszonych).
   - Historia zmian (ostatnie 10 wpisów z logów audytu po wdrożeniu `audit.logs.view`).

3. **User Overrides (`admin.roles.users`)**
   - Reguła: tylko użytkownicy z `users.assign-roles` lub `users.assign-permissions`.
   - Integracja z istniejącym `Members` przez dodatkową zakładkę lub osobny ekran.
   - Lista użytkowników wraz z: rolą podstawową, dodatkowymi rolami, ręcznymi uprawnieniami (granted / revoked).
   - Formularz przypisywania: autocomplete ról i permów, znacznik ważności (np. czasowe nadanie).

4. **Permission Catalog (`admin.permissions.index`)**
   - Katalog wszystkich `module.action` z opisami.
   - Statystyki: ile ról/użytkowników korzysta, ostatnie użycie (wymaga logów lub tabeli auditów w przyszłości).
   - Akcje masowe: przypisz do roli X, usuń z roli Y, eksport listy.

## 3. UX i interakcje
- Używamy komponentów Flux (`flux:navlist`, `flux:table`, `flux:modal`) dla spójności.
- Matrix uprawnień: sticky header (kategorie) + wiersze pogrupowane (Panel, Users, Equipment...). Wiersz ma szybkie przełączniki "Zaznacz wszystkie w kategorii".
- Filtrowanie debounced Livewire (300 ms) dla wyszukiwania ról/permów.
- Notyfikacje Livewire emitowane przez `dispatch('notify', ...)` tak jak w `Members`.

## 4. Backend/Livewire
| Zadanie | Szczegóły |
| --- | --- |
| Modele | Rozszerzyć `Role`/`Permission` opisem i kategorią (np. kolumny `display_name`, `category`). Można wykonać migrację dodając te kolumny lub trzymać mapę w configu. |
| Komponenty | `Roles\Index`, `Roles\Show`, `Roles\Users`, `Permissions\Index`. Wszystkie korzystają z paginacji i autoryzacji (`Gate::authorize('roles.manage')`). |
| API | Akcje (create/update/delete role, syncPermissions, assignRole, givePermissionTo) wykonywane bezpośrednio przez modele Spatie. |
| Audyt | Docelowo logować zmiany (np. model `RoleAuditLog` lub wykorzystać `activitylog`). |

## 5. Stany i bezpieczeństwo
- Zablokować edycję ról krytycznych (`admin`, `koordynator`) – tylko zmiana opisu.
- Przy nadawaniu permissionów wymusić walidację `Rule::in(Permission::pluck('name'))`.
- Dodać confirm dialog przed usunięciem roli; zabronić jeśli rola posiada przypisanych użytkowników (lub wymusić reasignację).

## 6. Roadmapa implementacji
1. **Migracje & modele** – opcjonalne pola `display_name`, `category`, ewentualnie `description` + seed opisów.
2. **Komponent Roles Index** – listowanie + statystyki + dodawanie/duplikowanie roli.
3. **Role Detail** – matrix uprawnień, historia zmian.
4. **User Overrides** – rozszerzenie `Members` lub nowy komponent.
5. **Permission Catalog** – dokumentacja i masowe operacje.
6. **QA** – testy Pest (sprawdzenie assign/sync logicznej) + testy Livewire dla komponentów.

## 7. Wymagania dodatkowe
- Upewnić się, że wszystkie nowe trasy chronione są `AdminMiddleware` oraz gate `accessAdminPanel`.
- Rozważyć API JSON dla dynamicznych filtrów (np. `/api/permissions?category=equipment`).
- Przygotować dokumentację użytkową (aktualizacja `MIGRATION_GUIDE.md` + mini tutorial w panelu).
