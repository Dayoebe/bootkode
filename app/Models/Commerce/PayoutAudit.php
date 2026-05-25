<?php

namespace App\Models\Commerce;

use App\Models\Core\User;
use App\Models\Marketplace\WalletTransaction;
use App\Models\Marketplace\Withdrawal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'withdrawal_id',
        'wallet_transaction_id',
        'user_id',
        'actor_id',
        'event',
        'status_from',
        'status_to',
        'amount',
        'currency',
        'provider',
        'provider_reference',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class);
    }

    public function walletTransaction()
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        $symbol = $this->currency === 'NGN' ? '₦' : $this->currency . ' ';

        return $symbol . number_format((float) $this->amount, 2);
    }
}
