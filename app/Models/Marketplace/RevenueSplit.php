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
        if (!is_finite($amount) || $amount < 0 || !$this->is_active
            || (float) $this->instructor_percentage < 0 || (float) $this->platform_percentage < 0
            || abs((float) $this->instructor_percentage + (float) $this->platform_percentage - 100) > 0.001) {
            throw new \DomainException('Course revenue split is invalid.');
        }
        $totalMinor = (int) round($amount * 100);
        $instructorMinor = (int) round($totalMinor * (float) $this->instructor_percentage / 100);
        $instructorAmount = $instructorMinor / 100;
        $platformAmount = ($totalMinor - $instructorMinor) / 100;

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
            'instructor_percentage' => (float) config('financial.default_instructor_percentage', 80),
            'platform_percentage' => 100 - (float) config('financial.default_instructor_percentage', 80)
        ];
    }
}
