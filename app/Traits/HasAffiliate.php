<?php

// app/Traits/HasAffiliate.php
namespace App\Traits;

use App\Models\Marketplace\Affiliate;
use App\Models\Marketplace\Referral;
use App\Models\Marketplace\ReferralTransaction;

trait HasAffiliate
{
    // Affiliate relationship (if user is an affiliate)
    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    // Referrals made by this user (if they're an affiliate)
    public function referrals()
    {
        return $this->hasManyThrough(Referral::class, Affiliate::class);
    }

    // Users referred by this user
    public function referredUsers()
    {
        return $this->hasManyThrough(\App\Models\Core\User::class, Referral::class, 'affiliate_id', 'id', 'id', 'referred_user_id')
                    ->through('affiliate');
    }

    // If this user was referred by someone
    public function referrer()
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'referred_by');
    }

    // Get this user's referral record (if they were referred)
    public function referralRecord()
    {
        return $this->hasOne(Referral::class, 'referred_user_id');
    }

    // Check if user is an affiliate
    public function isAffiliate(): bool
    {
        return $this->affiliate !== null && $this->affiliate->isActive();
    }

    // Check if user was referred
    public function wasReferred(): bool
    {
        return $this->referred_by !== null;
    }

    // Get or create affiliate account
    public function getOrCreateAffiliate(float $commissionRate = 30.00): Affiliate
    {
        return $this->affiliate ?: $this->affiliate()->create([
            'commission_rate' => $commissionRate,
            'status' => Affiliate::STATUS_ACTIVE
        ]);
    }

    // Get affiliate dashboard stats
    public function getAffiliateStats(): array
    {
        if (!$this->isAffiliate()) {
            return [
                'is_affiliate' => false,
                'total_earned' => 0,
                'total_referrals' => 0,
                'active_referrals' => 0,
                'pending_commissions' => 0,
                'referral_link' => null,
                'formatted_total_earned' => '₦0.00'
            ];
        }

        $affiliate = $this->affiliate;
        $pendingCommissions = ReferralTransaction::whereHas('referral', function($query) use ($affiliate) {
                $query->where('affiliate_id', $affiliate->id);
            })
            ->where('status', ReferralTransaction::STATUS_PENDING)
            ->sum('commission_amount');

        return [
            'is_affiliate' => true,
            'referral_code' => $affiliate->referral_code,
            'total_earned' => $affiliate->total_earned,
            'total_referrals' => $affiliate->total_referrals,
            'active_referrals' => $affiliate->active_referrals,
            'pending_commissions' => $pendingCommissions,
            'referral_link' => $affiliate->referral_link,
            'formatted_total_earned' => $affiliate->formatted_total_earned,
            'commission_rate' => $affiliate->commission_rate,
            'status' => $affiliate->status,
        ];
    }

    // Get commission wallet transactions
    public function getCommissionTransactions($limit = null)
    {
        $walletIds = $this->wallet ? [$this->wallet->id] : [];
        if ($this->instructorWallet) {
            $walletIds[] = $this->instructorWallet->id;
        }

        $query = \App\Models\Marketplace\WalletTransaction::whereIn('wallet_id', $walletIds)
            ->where('category', 'referral_commission')
            ->with('transactionable')
            ->orderBy('created_at', 'desc');

        return $limit ? $query->limit($limit)->get() : $query->get();
    }

    // Get referred users activity
    public function getReferredUsersActivity($limit = null)
    {
        if (!$this->isAffiliate()) {
            return collect();
        }

        $query = $this->referrals()
            ->with(['referredUser', 'transactions' => function($q) {
                $q->with('course')->latest();
            }])
            ->orderBy('created_at', 'desc');

        return $limit ? $query->limit($limit)->get() : $query->get();
    }
}
