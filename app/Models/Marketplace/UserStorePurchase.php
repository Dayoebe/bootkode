<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User;

class UserStorePurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'reward_store_item_id', 'is_equipped'
    ];

    protected $casts = [
        'is_equipped' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(RewardStoreItem::class, 'reward_store_item_id');
    }
}