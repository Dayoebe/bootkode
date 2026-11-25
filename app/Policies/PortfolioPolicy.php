<?php

namespace App\Policies;

use App\Models\Content\Portfolio;
use App\Models\Core\User;

class PortfolioPolicy
{
    public function create(User $user): bool
    {
        return $user->isStudent() && $user->is_active && $user->deactivated_at === null;
    }

    public function view(User $user, Portfolio $portfolio): bool
    {
        return $this->owns($user, $portfolio);
    }

    public function update(User $user, Portfolio $portfolio): bool
    {
        return $this->owns($user, $portfolio);
    }

    public function delete(User $user, Portfolio $portfolio): bool
    {
        return $this->owns($user, $portfolio);
    }

    public function reorder(User $user, Portfolio $portfolio): bool
    {
        return $this->owns($user, $portfolio);
    }

    public function publish(User $user, Portfolio $portfolio): bool
    {
        return $this->owns($user, $portfolio);
    }

    private function owns(User $user, Portfolio $portfolio): bool
    {
        return $this->create($user) && (int) $portfolio->user_id === (int) $user->id;
    }
}
