<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User;

class DownloadableContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'downloaded_at',
        'last_accessed_at',
        'last_synced_at',
        'size_mb',
        'storage_limit_mb',
        'storage_bytes',
        'cached_asset_count',
        'status',
        'manifest',
        'content_types' // JSON field to store types of content (lessons, pdfs, etc.)
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'content_types' => 'array',
        'manifest' => 'array',
        'size_mb' => 'decimal:2',
        'storage_limit_mb' => 'integer',
        'storage_bytes' => 'integer',
        'cached_asset_count' => 'integer',
    ];

    public const STATUS_READY = 'ready';
    public const STATUS_CACHED = 'cached';
    public const STATUS_SYNCED = 'synced';
    public const STATUS_STALE = 'stale';

    public function user()
    {
        return $this->belongsTo(User::class); 
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getStorageUsagePercentageAttribute(): float
    {
        if ((int) $this->storage_limit_mb <= 0) {
            return 0;
        }

        return round(((float) $this->size_mb / (int) $this->storage_limit_mb) * 100, 1);
    }
}
