<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MigrateUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder migrates existing users from the old 'role' field to Spatie Permission system
     */
    public function run(): void
    {
        $this->command->info('Starting user roles migration...');
        
        // Get all users
        $users = User::all();
        
        $migrated = 0;
        $skipped = 0;
        
        foreach ($users as $user) {
            // Skip if user already has roles assigned
            if ($user->roles->count() > 0) {
                $skipped++;
                continue;
            }
            
            // Map old role field to new role system
            $roleName = match($user->role) {
                'admin' => 'admin',
                'instructor' => 'instructor',
                'user' => 'student',  // Map 'user' to 'student'
                default => 'student',  // Default to student for any unknown roles
            };
            
            try {
                $user->assignRole($roleName);
                $migrated++;
                $this->command->info("Migrated user {$user->name} ({$user->email}) to role: {$roleName}");
            } catch (\Exception $e) {
                $this->command->error("Failed to migrate user {$user->name}: " . $e->getMessage());
            }
        }
        
        $this->command->info("Migration complete!");
        $this->command->info("Migrated: {$migrated} users");
        $this->command->info("Skipped: {$skipped} users (already have roles)");
    }
}
