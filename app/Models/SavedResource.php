<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'resourceable_type',
        'resourceable_id',
        'type',
        'notes'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function resourceable(): MorphTo
    {
        return $this->morphTo();
    }
}