<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Group;

// Pobierz istniejące grupy lub utwórz nowe
$groups = Group::all();
if ($groups->isEmpty()) {
    $groupAdmin = Group::create([
        'name' => 'Administracja',
        'description' => 'Grupa administratorów systemu',
        'active' => true,
    ]);

    $groupUsers = Group::create([
        'name' => 'Użytkownicy',
        'description' => 'Klasa IVOPW 2025/2026',
        'active' => true,
    ]);
    
    $groupIds = [$groupAdmin->id, $groupUsers->id, null];
} else {
    $groupIds = $groups->pluck('id')->toArray();
    $groupIds[] = null; // Dodaj opcję bez grupy
}

echo "Tworzenie 50 użytkowników...\n";

for ($i = 0; $i < 50; $i++) {
    $user = User::factory()->create([
        'group_id' => $groupIds[array_rand($groupIds)],
    ]);
    echo ($i + 1) . ". Utworzono: {$user->name} ({$user->email})\n";
}

echo "\nGotowe! Utworzono 50 użytkowników.\n";
