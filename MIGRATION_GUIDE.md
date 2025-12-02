# Migracja do systemu ról i uprawnień Spatie Permission

## Wykonane zmiany

### 1. Instalacja pakietu
- Zainstalowano `spatie/laravel-permission` v6.23.0
- Opublikowano konfigurację i migracje

### 2. Aktualizacja modelu User
- Dodano trait `HasRoles` z pakietu Spatie
- Model teraz obsługuje role i uprawnienia

### 3. Utworzone role i uprawnienia

#### Role:
- **admin** - pełny dostęp do wszystkich funkcji
- **instructor** - zarządzanie treściami i przeglądanie użytkowników
- **student** - podstawowy dostęp (przeglądanie postów, komentowanie)
- **guest** - minimalny dostęp (tylko przeglądanie)

#### Uprawnienia:
- `view posts`, `create posts`, `edit posts`, `delete posts`, `publish posts`
- `view comments`, `create comments`, `edit comments`, `delete comments`, `moderate comments`
- `view users`, `create users`, `edit users`, `delete users`, `manage user roles`
- `view groups`, `create groups`, `edit groups`, `delete groups`
- `access admin panel`, `view dashboard stats`

### 4. Zaktualizowane pliki

#### Backend:
- `app/Providers/AuthServiceProvider.php` - dodano gates: `isAdmin`, `isInstructor`, `accessAdminPanel`
- `app/Http/Middleware/AdminMiddleware.php` - zmieniono na sprawdzanie `can('access admin panel')`
- `app/Livewire/Settings/Profile.php` - `hasRole(['admin', 'instructor'])` zamiast `in_array`
- `app/Livewire/Admin/Members.php` - pełna integracja z systemem ról Spatie

#### Frontend:
- `resources/views/components/layouts/app/usersidebar.blade.php` - `@can('access admin panel')` zamiast `@if(in_array(...))`
- `resources/views/components/layouts/app/sidebar.blade.php` - `@can('view users')` zamiast `@if(in_array(...))`
- `resources/views/livewire/admin/members.blade.php` - wyświetlanie ról z systemu Spatie

#### Seeders:
- `RolesAndPermissionsSeeder.php` - tworzy role i przypisuje uprawnienia
- `MembersSeeder.php` - przypisuje role nowym użytkownikom
- `MigrateUserRolesSeeder.php` - migruje istniejących użytkowników

## Instrukcja wdrożenia

### Szybka instalacja (zalecana dla nowej bazy)

**Uruchom skrypt seedowania:**

PowerShell:
```powershell
.\seed_database.ps1
```

Lub CMD:
```cmd
seed_database.bat
```

Skrypt automatycznie:
1. Zaseeduje role i uprawnienia
2. Utworzy użytkowników, grupy, posty, reakcje i komentarze
3. Zresetuje cache uprawnień
4. Wyczyści cache aplikacji

---

### Ręczna instalacja (krok po kroku)

### Krok 1: Uruchom XAMPP
Upewnij się, że Apache i MySQL są uruchomione.

### Krok 2: Uruchom migracje
```powershell
php artisan migrate
```
To utworzy tabele: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

### Krok 3: Seeduj role i uprawnienia
```powershell
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Krok 4: Seeduj dane testowe
```powershell
php artisan db:seed --class=MembersSeeder
```
To utworzy:
- **1 administratora**: admin@example.com / Haslo1234
- **2 instruktorów**: jan.kowalski@example.com, anna.nowak@example.com / Haslo1234
- **1 wychowawcę**: piotr.wisniewski@example.com / Haslo1234
- **30 studentów** w grupie 4OPW (student1@example.com - student30@example.com / Haslo1234)
- **20 postów** z losową treścią
- **60 reakcji** (polubienia/niepolubienia) na posty
- **50 komentarzy** pod postami

### Krok 5: Migruj istniejących użytkowników (tylko jeśli masz starą bazę)
```powershell
php artisan db:seed --class=MigrateUserRolesSeeder
```
Ten seeder przypisze role użytkownikom na podstawie ich obecnego pola 'role':
- `admin` → rola `admin`
- `instructor` → rola `instructor`
- `user` → rola `student`

**UWAGA:** Pomiń ten krok jeśli tworzysz nową bazę danych!

### Krok 6: Zresetuj cache uprawnień
```powershell
php artisan permission:cache-reset
```

### Krok 7 (opcjonalnie): Przeładuj cache aplikacji
```powershell
php artisan config:clear
php artisan cache:clear
```

## Użycie w kodzie

### W kontrolerach i komponentach Livewire:
```php
// Sprawdzenie roli
if (auth()->user()->hasRole('admin')) { ... }
if (auth()->user()->hasRole(['admin', 'instructor'])) { ... }

// Sprawdzenie uprawnienia
if (auth()->user()->can('create posts')) { ... }

// Przypisanie roli
$user->assignRole('student');
$user->syncRoles(['admin']); // usuwa wszystkie inne role
```

### W blade templates:
```blade
@can('access admin panel')
    <!-- Treść dla użytkowników z dostępem do panelu -->
@endcan

@role('admin')
    <!-- Treść tylko dla adminów -->
@endrole

@hasrole('admin|instructor')
    <!-- Treść dla adminów lub instruktorów -->
@endhasrole
```

### W middleware i routes:
```php
// W routes/web.php
Route::middleware(['role:admin'])->group(function () {
    // Trasy tylko dla adminów
});

Route::middleware(['permission:create posts'])->group(function () {
    // Trasy dla użytkowników z uprawnieniem
});
```

## Usunięcie starego pola 'role'

Po upewnieniu się, że wszystko działa poprawnie, możesz usunąć stare pole 'role' z tabeli users:

```powershell
php artisan make:migration remove_role_from_users_table
```

Następnie w migracji:
```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('student');
    });
}
```

## Dane testowe

Po uruchomieniu seedera `MembersSeeder` system utworzy:

### Użytkownicy:
- **Administrator**: admin@example.com / Haslo1234 (grupa: Administracja)
- **Instruktor 1**: jan.kowalski@example.com / Haslo1234 (grupa: Administracja)
- **Instruktor 2**: anna.nowak@example.com / Haslo1234 (grupa: Administracja)
- **Wychowawca**: piotr.wisniewski@example.com / Haslo1234 (grupa: 4OPW)
- **30 Studentów**: student1@example.com - student30@example.com / Haslo1234 (grupa: 4OPW)

### Grupy:
- **Administracja** - grupa dla adminów i instruktorów
- **4OPW** - klasa uczniów 2025/2026

### Zawartość:
- **20 postów** - losowo przypisane do autorów (admin, instruktorzy, wychowawca)
- **60 reakcji** - losowe polubienia/niepolubienia postów przez użytkowników
- **50 komentarzy** - losowe komentarze pod postami

Wszystkie konta mają to samo hasło: **Haslo1234**

## Uwagi
- Stare pole 'role' jest nadal w bazie danych dla bezpieczeństwa
- System Spatie Permission pozwala na przypisanie wielu ról jednemu użytkownikowi
- Cache uprawnień jest automatycznie odświeżany, ale można to zrobić ręcznie: `php artisan permission:cache-reset`
- Dokumentacja Spatie: https://spatie.be/docs/laravel-permission/
