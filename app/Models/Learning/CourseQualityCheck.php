<?php

namespace App\Models\Learning;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseQualityCheck extends Model
{
    protected $fillable = [
        'course_id',
        'checked_by',
        'score',
        'status',
        'public_label',
        'completeness_percent',
        'assessment_coverage_percent',
        'media_health_percent',
        'freshness_percent',
        'broken_media_count',
        'unchecked_external_media_count',
        'remote_media_checked',
        'issues',
        'media_results',
        'summary',
        'checked_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'completeness_percent' => 'integer',
        'assessment_coverage_percent' => 'integer',
        'media_health_percent' => 'integer',
        'freshness_percent' => 'integer',
        'broken_media_count' => 'integer',
        'unchecked_external_media_count' => 'integer',
        'remote_media_checked' => 'boolean',
        'issues' => 'array',
        'media_results' => 'array',
        'summary' => 'array',
        'checked_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
