<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Core\User;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_type',
        'balance',
        'pending_balance',
        'currency',
        'is_active',
        'last_activity'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'last_activity' => 'datetime'
    ];

    const TYPE_USER = 'user';
    const TYPE_INSTRUCTOR = 'instructor';
    const TYPE_PLATFORM = 'platform';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function credit(float $amount, string $category, string $description, $transactionable = null, array $metadata = []): WalletTransaction
    {
        return $this->recordMovement($amount, 'credit', $category, $description, $transactionable, $metadata);
    }

    public function debit(float $amount, string $category, string $description, $transactionable = null, array $metadata = []): WalletTransaction
    {
        return $this->recordMovement($amount, 'debit', $category, $description, $transactionable, $metadata);
    }

    private function recordMovement(float $amount, string $type, string $category, string $description, $transactionable, array $metadata): WalletTransaction
    {
        if (!is_finite($amount) || $amount <= 0 || round($amount * 100) < 1) {
            throw new \InvalidArgumentException('Wallet amount must be positive.');
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($amount, $type, $category, $description, $transactionable, $metadata) {
            $wallet = static::whereKey($this->getKey())->lockForUpdate()->firstOrFail();
            if (!$wallet->is_active) {
                throw new \DomainException('Wallet is inactive.');
            }
            // Compare integer minor units, including for callers holding a stale model.
            $minorAmount = (int) round($amount * 100);
            $before = (int) round((float) $wallet->balance * 100);
            if ($type === 'debit' && $before < $minorAmount) {
                throw new \DomainException('Insufficient wallet balance');
            }
            $after = $type === 'credit' ? $before + $minorAmount : $before - $minorAmount;
            $wallet->update(['balance' => $after / 100, 'last_activity' => now()]);
            $transaction = $wallet->transactions()->create([
                'transaction_id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => $type, 'category' => $category, 'amount' => $minorAmount / 100,
                'balance_before' => $before / 100, 'balance_after' => $after / 100,
                'description' => $description, 'metadata' => $metadata,
                'transactionable_type' => $transactionable?->getMorphClass(),
                'transactionable_id' => $transactionable?->getKey(),
            ]);
            $this->setRawAttributes($wallet->getAttributes(), true);

            return $transaction;
        });
    }

    // Check if wallet has sufficient balance
    public function hasSufficientBalance(float $amount): bool
    {
        return (float) ($this->balance ?? 0) >= $amount;
    }

    // Get formatted balance
    public function getFormattedBalanceAttribute(): string
    {
        return '₦' . number_format((float) ($this->balance ?? 0), 2);
    }

    // Static method to get or create wallet
    public static function getOrCreateWallet(int $userId, string $walletType = self::TYPE_USER): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId, 'wallet_type' => $walletType],
            [
                'balance' => 0,
                'pending_balance' => 0,
                'currency' => 'NGN',
                'is_active' => true,
            ]
        );
    }
}
