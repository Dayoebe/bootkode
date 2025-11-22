<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User; 

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 
        'user_id', 
        'enrolled_at', 
        'progress_percentage', 
        'is_completed', 
        'completed_at',
        'enrollment_type',  // NEW
        'amount_paid'       // NEW
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
        'amount_paid' => 'decimal:2',  // NEW
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Check if enrollment is eligible for refund
     */
    public function isRefundEligible(): bool
    {
        // Within 7 days and less than 10% progress
        return $this->enrolled_at->diffInDays(now()) <= 7 
            && $this->progress_percentage < 10 
            && $this->amount_paid > 0;
    }
    
    /**
     * Get formatted amount paid
     */
    public function getFormattedAmountPaidAttribute(): string
    {
        return '₦' . number_format($this->amount_paid, 2);
    }
}