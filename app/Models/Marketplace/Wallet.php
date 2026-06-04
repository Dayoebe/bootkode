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

    // Credit wallet balance
    public function credit(float $amount, string $category, string $description, $transactionable = null, array $metadata = []): WalletTransaction
    {
        $balanceBefore = (float) ($this->balance ?? 0);
        $balanceAfter = $balanceBefore + $amount;

        $this->update([
            'balance' => $balanceAfter,
            'last_activity' => now()
        ]);

        $transactionData = [
            'transaction_id' => \Str::uuid(),
            'type' => 'credit',
            'category' => $category,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'metadata' => $metadata,
        ];

        // Add polymorphic relationship if transactionable provided
        if ($transactionable) {
            $transactionData['transactionable_type'] = get_class($transactionable);
            $transactionData['transactionable_id'] = $transactionable->id;
        }

        return $this->transactions()->create($transactionData);
    }

    // Debit wallet balance
    public function debit(float $amount, string $category, string $description, $transactionable = null, array $metadata = []): WalletTransaction
    {
        $balanceBefore = (float) ($this->balance ?? 0);

        if ($balanceBefore < $amount) {
            throw new \Exception('Insufficient wallet balance');
        }

        $balanceAfter = $balanceBefore - $amount;

        $this->update([
            'balance' => $balanceAfter,
            'last_activity' => now()
        ]);

        $transactionData = [
            'transaction_id' => \Str::uuid(),
            'type' => 'debit',
            'category' => $category,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'metadata' => $metadata,
        ];

        // Add polymorphic relationship if transactionable provided
        if ($transactionable) {
            $transactionData['transactionable_type'] = get_class($transactionable);
            $transactionData['transactionable_id'] = $transactionable->id;
        }

        return $this->transactions()->create($transactionData);
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
