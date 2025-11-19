<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tworzymy przykładowe grupy
        $groupAdmin = Group::create([
            'name' => 'Administracja',
            'description' => 'Grupa administratorów systemu',
        ]);

        $groupUsers = Group::create([
            'name' => 'Użytkownicy',
            'description' => 'Klasa IVOPW 2025/2026',
        ]);

        // Tworzymy administratora
        User::create([
            'name' => 'Admin Systemu',
            'email' => 'admin@example.com',
            'password' => Hash::make('Haslo1234'), // zmień na bezpieczne hasło
            'role' => 'admin',
            'pilot_license' => 'PL-ADMIN-001',
            'operator_license' => 'OP-ADMIN-001',
            'license_expiry_date' => now()->addYear(),
            'active' => true,
            'group_id' => $groupAdmin->id,
        ]);

        // Tworzymy 50 losowych użytkowników
        User::factory()->count(50)->create([
            'group_id' => fake()->randomElement([$groupAdmin->id, $groupUsers->id, null]),
        ]);
    }
}
