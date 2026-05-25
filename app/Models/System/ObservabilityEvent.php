<?php

namespace App\Models\System;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityEvent extends Model
{
    public const TYPE_ERROR = 'error';
    public const TYPE_FAILED_JOB = 'failed_job';
    public const TYPE_MAIL_FAILURE = 'mail_failure';
    public const TYPE_WEBHOOK_FAILURE = 'webhook_failure';
    public const TYPE_SLOW_PAGE = 'slow_page';
    public const TYPE_BROKEN_ROUTE = 'broken_route';

    public const STATUS_OPEN = 'open';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_IGNORED = 'ignored';

    public const SEVERITY_INFO = 'info';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_ERROR = 'error';
    public const SEVERITY_CRITICAL = 'critical';

    protected $fillable = [
        'type',
        'severity',
        'status',
        'source',
        'summary',
        'message',
        'url',
        'method',
        'route_name',
        'user_id',
        'ip_address',
        'user_agent',
        'duration_ms',
        'fingerprint',
        'occurrences',
        'context',
        'first_seen_at',
        'last_seen_at',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'context' => 'array',
        'duration_ms' => 'integer',
        'occurrences' => 'integer',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_FAILED_JOB => 'Failed job',
            self::TYPE_MAIL_FAILURE => 'Mail failure',
            self::TYPE_WEBHOOK_FAILURE => 'Webhook failure',
            self::TYPE_SLOW_PAGE => 'Slow page',
            self::TYPE_BROKEN_ROUTE => 'Broken route',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
