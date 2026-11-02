<?php

namespace App\Services\Career;

use App\Models\Core\User;

class CareerAccess
{
    public function account(): User
    {
        $user = User::find(auth()->id());
        abort_unless($user?->is_active && $user->hasVerifiedEmail(), 403);

        return $user;
    }

    public function administrator(): User
    {
        $user = $this->account();
        abort_unless($user->hasAnyRole(['super_admin', 'academy_admin']), 403);

        return $user;
    }

    public function owner(User $user): User
    {
        $actor = $this->account();
        abort_unless((int) $actor->id === (int) $user->id, 403);

        return $actor;
    }
}
