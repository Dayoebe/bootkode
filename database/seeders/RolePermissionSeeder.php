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
            'manage-roles',
            // Blog permissions
            'manage_blog_posts',
            'create_blog_posts',
            'manage_blog_categories',
            'moderate_blog_comments',
            'manage_blog_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => User::ROLE_SUPER_ADMIN]);
        $superAdmin->syncPermissions($permissions); // All permissions

        $academyAdmin = Role::firstOrCreate(['name' => User::ROLE_ACADEMY_ADMIN]);
        $academyAdmin->syncPermissions([
            'manage_certificates',
            'manage_certificate_templates',
            'manage_courses',
            'manage_users',
            'edit_users',
            'manage_blog_posts',
            'create_blog_posts',
            'manage_blog_categories',
            'moderate_blog_comments',
        ]);

        $contentEditor = Role::firstOrCreate(['name' => User::ROLE_CONTENT_EDITOR]);
        $contentEditor->syncPermissions([
            'manage_courses',
            'view_courses',
            'manage_blog_posts',
            'create_blog_posts',
            'moderate_blog_comments',
        ]);

        $instructor = Role::firstOrCreate(['name' => User::ROLE_INSTRUCTOR]);
        $instructor->syncPermissions([
            'manage_courses',
            'view_courses',
            'create_blog_posts', // Instructors can create blog posts
        ]);

        $mentor = Role::firstOrCreate(['name' => User::ROLE_MENTOR]);
        $mentor->syncPermissions([
            'view_courses',
        ]);

        $affiliateAmbassador = Role::firstOrCreate(['name' => User::ROLE_AFFILIATE_AMBASSADOR]);
        $affiliateAmbassador->syncPermissions([
            'view_courses',
        ]);

        $student = Role::firstOrCreate(['name' => User::ROLE_STUDENT]);
        $student->syncPermissions([
            'view_own_certificates',
            'request_certificates',
            'view_courses',
        ]);

        // Sync existing users with their roles
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role && in_array($user->role, User::getRoles())) {
                $user->syncRoles([$user->role]);
            }
        }
    }
}
