<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'color', 'order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    public function threads()
    {
        return $this->hasMany(ForumThread::class, 'category_id');
    }

    public function latestThread()
    {
        return $this->hasOne(ForumThread::class, 'category_id')->latest('last_activity_at');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}

