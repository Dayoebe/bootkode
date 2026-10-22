<?php

namespace Database\Factories\Core;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        Role::findOrCreate(User::ROLE_STUDENT, 'web');

        return [
            'name' => fake()->name(), 'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(), 'password' => Hash::make('password'),
            'remember_token' => Str::random(10), 'role' => User::ROLE_STUDENT, 'is_active' => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
