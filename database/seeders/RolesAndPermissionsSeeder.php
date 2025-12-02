<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Content permissions
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
            
            // Comment permissions
            'view comments',
            'create comments',
            'edit comments',
            'delete comments',
            'moderate comments',
            
            // User management permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',
            
            // Group permissions
            'view groups',
            'create groups',
            'edit groups',
            'delete groups',
            
            // Admin dashboard
            'access admin panel',
            'view dashboard stats',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin - full access
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Instructor - can manage content and view users
        $instructor = Role::create(['name' => 'instructor']);
        $instructor->givePermissionTo([
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
            'view comments',
            'create comments',
            'moderate comments',
            'view users',
            'view groups',
            'access admin panel',
            'view dashboard stats',
        ]);

        // Student - basic access
        $student = Role::create(['name' => 'student']);
        $student->givePermissionTo([
            'view posts',
            'view comments',
            'create comments',
        ]);

        // Guest - minimal access
        $guest = Role::create(['name' => 'guest']);
        $guest->givePermissionTo([
            'view posts',
            'view comments',
        ]);
    }
}
