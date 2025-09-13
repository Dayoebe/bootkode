<?php

// app/Policies/MarketplaceOrderPolicy.php
namespace App\Policies;

use App\Models\MarketplaceOrder;
use App\Models\User;

class MarketplaceOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own orders
    }

    public function view(User $user, MarketplaceOrder $order): bool
    {
        // Can view if customer, vendor, or admin
        return $order->customer_id === $user->id ||
               $order->vendor_id === $user->id ||
               $user->isSuperAdmin() || 
               $user->isAcademyAdmin();
    }

    public function update(User $user, MarketplaceOrder $order): bool
    {
        // Vendors can update their orders, admins can update any
        return $order->vendor_id === $user->id ||
               $user->isSuperAdmin() || 
               $user->isAcademyAdmin();
    }

    public function refund(User $user, MarketplaceOrder $order): bool
    {
        // Only admins can process refunds
        return $user->isSuperAdmin() || $user->isAcademyAdmin();
    }

    public function cancel(User $user, MarketplaceOrder $order): bool
    {
        // Customer can cancel pending orders, vendors can cancel confirmed orders, admins can cancel any
        return ($order->customer_id === $user->id && $order->isPending()) ||
               ($order->vendor_id === $user->id && $order->isConfirmed()) ||
               $user->isSuperAdmin() || 
               $user->isAcademyAdmin();
    }
}
