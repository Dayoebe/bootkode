<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Core\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'super@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('9638'),
                'email_verified_at' => now(),
                'date_of_birth' => '1998-01-29',
                'phone_number' => '09030036438',
                'bio' => 'Web developer and admin of BootKode.',
                'role' => User::ROLE_SUPER_ADMIN,

                // Optional fields with safe defaults
                'is_active' => true,
                'receive_course_updates' => true,
                'receive_certificate_notifications' => true,
            ]
        );

        // Assign the Super Admin role using Spatie Permission
        $user->syncRoles([User::ROLE_SUPER_ADMIN]);

        $this->command->info("✅ User '{$user->name}' created and assigned the Super Admin role.");
    }
}
