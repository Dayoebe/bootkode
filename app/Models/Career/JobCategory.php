<?php

namespace App\Models\Career;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
        'jobs_count'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function jobs()
    {
        return $this->hasMany(JobPortal::class, 'category', 'slug');
    }

    public function activeJobs()
    {
        return $this->hasMany(JobPortal::class, 'category', 'slug')->active();
    }

    // Methods
    public function updateJobsCount()
    {
        $this->update([
            'jobs_count' => $this->activeJobs()->count()
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

