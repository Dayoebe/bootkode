<?php

// MODEL 2: NewsletterCampaign.php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User;

class NewsletterCampaign extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENDING = 'sending';
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_CAMPAIGN = 'campaign';
    const TYPE_TEMPLATE = 'template';
    const TYPE_SETTINGS = 'settings';

    protected $fillable = [
        'name', 'subject', 'preview_text', 'html_content', 'from_name', 'from_email', 'reply_to',
        'status', 'type', 'recipient_filters', 'total_recipients', 'sent_count', 'open_count',
        'click_count', 'bounce_count', 'unsubscribe_count', 'scheduled_at', 'sent_at',
        'description', 'variables', 'is_default', 'created_by'
    ];

    protected $casts = [
        'recipient_filters' => 'array',
        'variables' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_default' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // In NewsletterCampaign.php
public function interactions()
{
    return $this->hasMany(NewsletterInteraction::class, 'campaign_id');
}
    // public function interactions()
    // {
    //     return $this->hasMany(NewsletterInteraction::class);
    // }

    public function subscribers()
    {
        return $this->belongsToMany(NewsletterSubscriber::class, 'newsletter_interactions')
            ->wherePivot('type', 'send');
    }

    // Campaign methods
    public function getOpenRateAttribute()
    {
        return $this->sent_count > 0 ? round(($this->open_count / $this->sent_count) * 100, 2) : 0;
    }

    public function getClickRateAttribute()
    {
        return $this->sent_count > 0 ? round(($this->click_count / $this->sent_count) * 100, 2) : 0;
    }

    public function canBeSent()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SCHEDULED]) 
               && $this->type === self::TYPE_CAMPAIGN;
    }

    public function markAsSending()
    {
        $this->update(['status' => self::STATUS_SENDING]);
    }

    public function markAsSent()
    {
        $this->update(['status' => self::STATUS_SENT, 'sent_at' => now()]);
    }

    // Template methods
    public function renderWithVariables($variables = [])
    {
        $content = $this->html_content;
        foreach ($variables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }
        return $content;
    }

    // Settings methods
    public static function getSetting($key, $default = null)
    {
        $settings = static::where('type', self::TYPE_SETTINGS)->first();
        if (!$settings || !$settings->variables) {
            return $default;
        }
        return data_get($settings->variables, $key, $default);
    }

    public static function setSetting($key, $value)
    {
        $settings = static::firstOrCreate(
            ['type' => self::TYPE_SETTINGS],
            [
                'name' => '_system_settings',
                'subject' => 'System Settings',
                'html_content' => '',
                'from_name' => 'System',
                'from_email' => 'system@bootkode.com',
                'created_by' => 1
            ]
        );

        $variables = $settings->variables ?? [];
        data_set($variables, $key, $value);
        $settings->update(['variables' => $variables]);
    }

    // Scopes
    public function scopeCampaigns($query)
    {
        return $query->where('type', self::TYPE_CAMPAIGN);
    }

    public function scopeTemplates($query)
    {
        return $query->where('type', self::TYPE_TEMPLATE);
    }

    public function scopeScheduledForSending($query)
    {
        return $query->campaigns()
            ->where('status', self::STATUS_SCHEDULED)
            ->where('scheduled_at', '<=', now());
    }
    
}
