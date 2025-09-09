<?php 
// MODEL 1: NewsletterSubscriber.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';
    const STATUS_BOUNCED = 'bounced';

    protected $fillable = [
        'email', 'first_name', 'last_name', 'status', 'tags', 'metadata', 
        'source', 'subscribed_at', 'unsubscribed_at', 'unsubscribe_token', 'ip_address'
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($subscriber) {
            $subscriber->unsubscribe_token = Str::random(64);
            if (!$subscriber->subscribed_at) {
                $subscriber->subscribed_at = now();
            }
        });
    }


public function interactions()
{
    return $this->hasMany(NewsletterInteraction::class, 'subscriber_id');
}

    public function campaigns()
    {
        return $this->belongsToMany(NewsletterCampaign::class, 'newsletter_interactions')
            ->wherePivot('type', 'send');
    }

    // Helper methods
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function unsubscribe()
    {
        $this->update([
            'status' => self::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now(),
        ]);
    }

    public function resubscribe()
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'unsubscribed_at' => null,
        ]);
    }

    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }
}

