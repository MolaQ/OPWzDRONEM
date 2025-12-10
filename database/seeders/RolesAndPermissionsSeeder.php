<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Panel
            'admin.panel.access',
            'dashboard.view',

            // Users
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.deactivate',
            'users.reset-2fa',
            'users.reset-password',
            'users.assign-roles',
            'users.assign-permissions',

            // Groups
            'groups.view',
            'groups.create',
            'groups.update',
            'groups.delete',
            'groups.assign-users',

            // Equipment
            'equipment.view',
            'equipment.create',
            'equipment.update',
            'equipment.delete',
            'equipment.change-status',
            'equipment.assign-to-set',
            'equipment.import',
            'equipment.export',
            'equipment.maintenance',
            'equipment.confirm-reservation',
            'equipment.cancel-any-reservation',
            'equipment.view-all-reservations',

            // Equipment sets
            'equipment-sets.view',
            'equipment-sets.create',
            'equipment-sets.update',
            'equipment-sets.delete',
            'equipment-sets.change-status',
            'equipment-sets.manage-items',
            'equipment-sets.rent-out',
            'equipment-sets.close-rental',

            // Rentals
            'rentals.view',
            'rentals.create',
            'rentals.extend',
            'rentals.close',
            'rentals.approve',
            'rentals.mark-damage',
            'rentals.manage-groups',

            // Content
            'posts.view',
            'posts.create',
            'posts.update',
            'posts.delete',
            'posts.publish',
            'comments.view',
            'comments.create',
            'comments.update',
            'comments.delete',
            'comments.moderate',

            // Course materials
            'course-materials.view',
            'course-materials.create',
            'course-materials.update',
            'course-materials.delete',
            'course-materials.approve',

            // Achievements (gwiazdki)
            'achievements.view',
            'achievements.assign',
            'achievements.remove',

            // Roles & Permissions management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Courses
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',

            // System & reports
            'settings.view',
            'settings.update',
            'roles.manage',
            'permissions.manage',
            'audit.logs.view',
            'exports.run',
            'exports.download',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission], ['guard_name' => 'web']);
        }

        $allPermissions = Permission::pluck('name')->toArray();

        $roleDefinitions = [
            'admin' => $allPermissions,
            'coordinator' => array_diff($allPermissions, ['roles.manage', 'permissions.manage', 'settings.update']),
            'director' => array_diff($allPermissions, ['roles.manage', 'permissions.manage']),
            'instructor' => [
                'admin.panel.access', 'dashboard.view',
                'users.view',
                'groups.view',
                'equipment.view', 'equipment.create', 'equipment.update', 'equipment.change-status', 'equipment.assign-to-set', 'equipment.maintenance', 'equipment.confirm-reservation', 'equipment.view-all-reservations',
                'equipment-sets.view', 'equipment-sets.create', 'equipment-sets.update', 'equipment-sets.manage-items', 'equipment-sets.change-status', 'equipment-sets.rent-out', 'equipment-sets.close-rental',
                'rentals.view', 'rentals.create', 'rentals.extend', 'rentals.close', 'rentals.mark-damage', 'rentals.manage-groups',
                'posts.view', 'posts.create', 'posts.update', 'posts.delete', 'posts.publish',
                'comments.view', 'comments.create', 'comments.update', 'comments.delete', 'comments.moderate',
                'course-materials.view', 'course-materials.create', 'course-materials.update', 'course-materials.delete', 'course-materials.approve',
                'achievements.view', 'achievements.assign', 'achievements.remove',
                'exports.run'
            ],
            'wychowawca' => [
                'admin.panel.access', 'dashboard.view',
                'users.view', 'groups.view', 'groups.assign-users',
                'rentals.view', 'rentals.create', 'rentals.close',
                'equipment.view', 'equipment-sets.view',
                'posts.view', 'comments.view', 'comments.create',
                'achievements.view'
            ],
            'nauczyciel' => [
                'admin.panel.access', 'dashboard.view',
                'posts.view', 'posts.create', 'posts.update',
                'comments.view', 'comments.create', 'comments.update',
                'course-materials.view', 'course-materials.create', 'course-materials.update'
            ],
            'student' => [
                'posts.view', 'comments.view', 'comments.create',
                'course-materials.view',
                'achievements.view'
            ],
            'guest' => [
                'posts.view', 'comments.view'
            ],
        ];

        foreach ($roleDefinitions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName], ['guard_name' => 'web']);
            $role->syncPermissions($permissionNames);
        }
    }
}
