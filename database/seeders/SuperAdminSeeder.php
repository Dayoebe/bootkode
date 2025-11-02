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
                'username' => 'superadmin',
                'password' => bcrypt('9638'),
                'email_verified_at' => now(),
                'gender' => 'male',
                'date_of_birth' => '1998-01-29',
                'bio' => 'web dev',
                'phone_number' => '09030036438',
                'role' => User::ROLE_SUPER_ADMIN,
            ]
        );

        $user->syncRoles([User::ROLE_SUPER_ADMIN]);

        $this->command->info("✅ User '{$user->name}' created and assigned the Super Admin role.");
    }
}
