<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $fillable = [
        'log_name',
        'event',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties',
        'batch_uuid',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer of the activity.
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include activities for a specific log.
     */
    public function scopeInLog($query, $logName)
    {
        return $query->where('log_name', $logName);
    }

    /**
     * Scope a query to only include activities by a specific causer.
     */
    public function scopeCausedBy($query, $causer)
    {
        return $query->where('causer_id', $causer->getKey())
            ->where('causer_type', get_class($causer));
    }

    /**
     * Scope a query to only include activities for a specific subject.
     */
    public function scopeForSubject($query, $subject)
    {
        return $query->where('subject_id', $subject->getKey())
            ->where('subject_type', get_class($subject));
    }

    /**
     * Get the activity's properties as a formatted string.
     */
    public function getPropertiesFormattedAttribute(): string
    {
        if (empty($this->properties)) {
            return 'No additional data';
        }

        return collect($this->properties)
            ->map(function ($value, $key) {
                return "{$key}: " . (is_array($value) ? json_encode($value) : $value);
            })
            ->implode(', ');
    }

    /**
     * Get the activity's event in a human-readable format.
     */
    public function getEventNameAttribute(): string
    {
        return match ($this->event) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
            'force_deleted' => 'Permanently Deleted',
            default => ucfirst(str_replace('_', ' ', $this->event)),
        };
    }
}