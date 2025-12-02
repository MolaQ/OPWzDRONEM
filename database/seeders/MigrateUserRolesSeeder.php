<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MigrateUserRolesSeeder extends Seeder
{
    /**
     * Deprecated: kept for backward compatibility, intentionally does nothing.
     */
    public function run(): void
    {
        $this->command->warn('[MigrateUserRolesSeeder] Deprecated: no action performed. Use RolesAndPermissionsSeeder + MembersSeeder on fresh DB.');
    }
}
