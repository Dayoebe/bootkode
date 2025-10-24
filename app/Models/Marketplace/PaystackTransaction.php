<?php
// PaystackTransaction.php
namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;

class PaystackTransaction extends Model
{
    protected $fillable = [
        'reference',
        'paystack_reference',
        'access_code',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'paystack_response',
        'customer_email',
        'customer_name',
        'transaction_type',
        'transactionable_type',
        'transactionable_id',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paystack_response' => 'array',
        'paid_at' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_ABANDONED = 'abandoned';

    const TYPE_WALLET_FUNDING = 'wallet_funding';
    const TYPE_WITHDRAWAL = 'withdrawal';

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function hasFailed(): bool
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_ABANDONED]);
    }

    // Get formatted amount
    public function getFormattedAmountAttribute(): string
    {
        return '₦' . number_format($this->amount, 2);
    }
}
