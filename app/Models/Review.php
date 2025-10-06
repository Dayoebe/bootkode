<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'reviewable_type', 'reviewable_id', 'rating',
        'title', 'comment', 'is_approved', 'approved_at', 'approved_by',
        'is_featured', 'helpful_count'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Methods
    public function approve($approverId = null)
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $approverId ?? auth()->id(),
        ]);

        // Update parent item rating
        if ($this->reviewable) {
            $this->reviewable->updateRating();
        }
    }

    public function isApproved()
    {
        return $this->is_approved;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}