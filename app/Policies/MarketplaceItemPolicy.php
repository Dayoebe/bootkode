<?php
// app/Policies/MarketplaceItemPolicy.php
namespace App\Policies;

use App\Models\MarketplaceItem;
use App\Models\User;

class MarketplaceItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Everyone can browse marketplace
    }

    public function view(User $user, MarketplaceItem $item): bool
    {
        // Can view if published OR if it's their own item OR if they're admin
        return $item->isPublished() || 
               $item->vendor_id === $user->id ||
               $user->isSuperAdmin() || 
               $user->isAcademyAdmin();
    }

    public function create(User $user): bool
    {
        // Only vendors can create items
        return $user->canManageCourses();
    }

    public function update(User $user, MarketplaceItem $item): bool
    {
        // Can update own items if not approved, or admins can always update
        return ($item->vendor_id === $user->id && !$item->isApproved()) ||
               $user->isSuperAdmin() || 
               $user->isAcademyAdmin();
    }

    public function delete(User $user, MarketplaceItem $item): bool
    {
        // Can delete own items if no orders exist, or admins can always delete
        return ($item->vendor_id === $user->id && !$item->orders()->exists()) ||
               $user->isSuperAdmin() || 
               $user->isAcademyAdmin();
    }

    public function approve(User $user, MarketplaceItem $item): bool
    {
        // Only admins can approve/reject items
        return $user->isSuperAdmin() || $user->isAcademyAdmin();
    }

    public function purchase(User $user, MarketplaceItem $item): bool
    {
        // Cannot purchase own items
        return $item->vendor_id !== $user->id && $item->isPublished();
    }
}
