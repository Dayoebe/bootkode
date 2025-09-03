<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'transaction_type', 'currency_type', 'amount',
        'source', 'description', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getRecentActivity($userId, $limit = 10)
    {
        return self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}