<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardStoreItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type', 'item_key', 'name', 'description', 'icon', 'image_url',
        'cost_coins', 'cost_gems', 'required_level', 'requirements',
        'is_available', 'is_limited_time', 'available_until', 'item_data'
    ];

    protected $casts = [
        'requirements' => 'array',
        'item_data' => 'array',
        'is_available' => 'boolean',
        'is_limited_time' => 'boolean',
        'available_until' => 'datetime',
    ];

    public function purchases()
    {
        return $this->hasMany(UserStorePurchase::class);
    }

    public function canUserPurchase(User $user)
    {
        $gamificationData = $user->gamificationData;
        if (!$gamificationData) return false;

        // Check level requirement
        if ($gamificationData->level < $this->required_level) {
            return false;
        }

        // Check currency
        if ($this->cost_coins > 0 && $gamificationData->coins < $this->cost_coins) {
            return false;
        }

        if ($this->cost_gems > 0 && $gamificationData->gems < $this->cost_gems) {
            return false;
        }

        // Check if already purchased
        if ($this->purchases()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }
}