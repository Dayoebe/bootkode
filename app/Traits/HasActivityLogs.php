<?php

namespace App\Traits;

use App\Models\System\ActivityLog;

trait HasActivityLogs
{
    public function logActivity(string $description, array $properties = [], string $event = 'custom', $subject = null)
    {
        return ActivityLog::create([
            'log_name' => strtolower(class_basename($this)),
            'event' => $event,
            'description' => $description,
            'subject_id' => $subject ? $subject->getKey() : $this->getKey(),
            'subject_type' => $subject ? get_class($subject) : get_class($this),
            'causer_id' => $this->getKey(),
            'causer_type' => get_class($this),
            'properties' => $properties,
        ]);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'causer_id')
            ->where('causer_type', get_class($this));
    }

    public function subjectActivities()
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
            ->where('subject_type', get_class($this));
    }
}