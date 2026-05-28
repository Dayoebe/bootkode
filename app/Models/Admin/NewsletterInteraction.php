<?php

// MODEL 3: NewsletterInteraction.php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterInteraction extends Model
{
    use HasFactory;

    const TYPE_SEND = 'send';
    const TYPE_OPEN = 'open';
    const TYPE_CLICK = 'click';
    const TYPE_BOUNCE = 'bounce';
    const TYPE_UNSUBSCRIBE = 'unsubscribe';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'campaign_id', 'subscriber_id', 'type', 'status', 'data', 'tracking_token',
        'ip_address', 'user_agent', 'error_message'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($interaction) {
            if (!$interaction->tracking_token && in_array($interaction->type, [self::TYPE_SEND, self::TYPE_OPEN, self::TYPE_CLICK])) {
                $interaction->tracking_token = Str::random(64);
            }
        });
    }
    // In NewsletterInteraction.php
public function campaign()
{
    return $this->belongsTo(NewsletterCampaign::class, 'campaign_id');
}


public function subscriber()
{
    return $this->belongsTo(NewsletterSubscriber::class, 'subscriber_id');
}


    // Helper methods
    public static function trackOpen($token)
    {
        $send = static::where('tracking_token', $token)
            ->where('type', self::TYPE_SEND)
            ->first();

        if ($send) {
            // Create open interaction if not exists
            $open = static::firstOrCreate([
                'campaign_id' => $send->campaign_id,
                'subscriber_id' => $send->subscriber_id,
                'type' => self::TYPE_OPEN,
            ], [
                'status' => self::STATUS_COMPLETED,
                'tracking_token' => $token,
            ]);

            // Increment campaign open count
            if ($open->wasRecentlyCreated) {
                $send->campaign->increment('open_count');
            }
        }
    }

    public static function trackClick($token, $url, $ipAddress = null, $userAgent = null)
    {
        $send = static::where('tracking_token', $token)
            ->where('type', self::TYPE_SEND)
            ->first();

        if ($send) {
            // Create click interaction
            static::create([
                'campaign_id' => $send->campaign_id,
                'subscriber_id' => $send->subscriber_id,
                'type' => self::TYPE_CLICK,
                'status' => self::STATUS_COMPLETED,
                'data' => ['url' => $url],
                'tracking_token' => $token,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            // Increment campaign click count
            $send->campaign->increment('click_count');
        }
    }

    public static function createSendRecord($campaignId, $subscriberId)
    {
        return static::create([
            'campaign_id' => $campaignId,
            'subscriber_id' => $subscriberId,
            'type' => self::TYPE_SEND,
            'status' => self::STATUS_PENDING,
        ]);
    }

    public function markAsSent()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
        $this->campaign->increment('sent_count');
    }

    // In NewsletterInteraction.php
public function markAsFailed($errorMessage = null)
{
    $this->update([
        'status' => self::STATUS_FAILED,
        'error_message' => $errorMessage,
    ]);
}

public function markAsBounced()
{
    $data = $this->data ?? [];
    $data['bounced_at'] = now()->toIso8601String();

    $this->update([
        'status' => self::STATUS_FAILED,
        'data' => $data,
    ]);

    $this->subscriber?->update([
        'status' => NewsletterSubscriber::STATUS_BOUNCED,
    ]);
}
 
    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePendingSends($query)
    {
        return $query->where('type', self::TYPE_SEND)
            ->where('status', self::STATUS_PENDING);
    }
}
