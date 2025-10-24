<?php
// RevenueSplit.php
namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Learning\Course; // UPDATED
use App\Models\Core\User;

class RevenueSplit extends Model
{
    protected $fillable = [
        'course_id',
        'instructor_id',
        'instructor_percentage',
        'platform_percentage',
        'is_active'
    ];

    protected $casts = [
        'instructor_percentage' => 'decimal:2',
        'platform_percentage' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Calculate split amounts
    public function calculateSplit(float $amount): array
    {
        $instructorAmount = ($amount * $this->instructor_percentage) / 100;
        $platformAmount = ($amount * $this->platform_percentage) / 100;

        return [
            'instructor_amount' => round($instructorAmount, 2),
            'platform_amount' => round($platformAmount, 2),
            'total_amount' => $amount
        ];
    }

    // Static method to get default split
    public static function getDefaultSplit(): array
    {
        return [
            'instructor_percentage' => 80.00,
            'platform_percentage' => 20.00
        ];
    }
}
