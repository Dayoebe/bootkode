<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'manage_certificates',
            'view_own_certificates',
            'request_certificates',
            'manage_certificate_templates',
            'manage_courses',
            'view_courses',
            'manage_users',
            'edit_users',
            'view_user_activity',
            'manage-roles', // Added
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => User::ROLE_SUPER_ADMIN]);
        $superAdmin->syncPermissions($permissions);

        $academyAdmin = Role::firstOrCreate(['name' => User::ROLE_ACADEMY_ADMIN]);
        $academyAdmin->syncPermissions([
            'manage_certificates',
            'manage_certificate_templates',
            'manage_courses',
            'manage_users',
            'edit_users',
        ]);

        $instructor = Role::firstOrCreate(['name' => User::ROLE_INSTRUCTOR]);
        $instructor->syncPermissions([
            'manage_courses',
            'view_courses',
        ]);

        $student = Role::firstOrCreate(['name' => User::ROLE_STUDENT]);
        $student->syncPermissions([
            'view_own_certificates',
            'request_certificates',
            'view_courses',
        ]);

        $users = User::all();
        foreach ($users as $user) {
            if ($user->role && in_array($user->role, User::getRoles())) {
                $user->syncRoles([$user->role]);
            }
        }
    }
}
// run this command to start
// php artisan db:seed --class=RolePermissionSeeder