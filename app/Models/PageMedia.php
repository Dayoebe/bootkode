<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PageMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'file_path',
        'file_size',
        'media_type',
        'width',
        'height',
        'alt_text',
        'description',
        'is_optimized',
        'thumbnails',
        'cdn_url',
        'folder',
        'tags',
        'uploaded_by',
    ];

    protected $casts = [
        'thumbnails' => 'array',
        'tags' => 'array',
        'is_optimized' => 'boolean',
    ];

    // Constants
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_DOCUMENT = 'document';
    const TYPE_OTHER = 'other';

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($media) {
            $media->uploaded_by = auth()->id();
            $media->media_type = $media->determineMediaType();
        });

        static::deleting(function ($media) {
            // Delete physical file and thumbnails
            $media->deleteFiles();
        });
    }

    // Relationships
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'page_media_attachments', 'media_id', 'page_id')
            ->withPivot(['context', 'sort_order'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeImages($query)
    {
        return $query->where('media_type', self::TYPE_IMAGE);
    }

    public function scopeVideos($query)
    {
        return $query->where('media_type', self::TYPE_VIDEO);
    }

    public function scopeDocuments($query)
    {
        return $query->where('media_type', self::TYPE_DOCUMENT);
    }

    public function scopeByFolder($query, $folder)
    {
        return $query->where('folder', $folder);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('original_name', 'like', "%{$search}%")
              ->orWhere('alt_text', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function determineMediaType(): string
    {
        $mimeType = $this->mime_type;
        
        if (str_starts_with($mimeType, 'image/')) {
            return self::TYPE_IMAGE;
        } elseif (str_starts_with($mimeType, 'video/')) {
            return self::TYPE_VIDEO;
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return self::TYPE_AUDIO;
        } elseif (in_array($mimeType, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return self::TYPE_DOCUMENT;
        }
        
        return self::TYPE_OTHER;
    }

    public function getUrl(): string
    {
        return $this->cdn_url ?: asset($this->file_path);
    }

    public function getThumbnailUrl($size = 'medium'): ?string
    {
        if ($this->media_type !== self::TYPE_IMAGE || !$this->thumbnails) {
            return null;
        }

        return isset($this->thumbnails[$size]) 
            ? asset($this->thumbnails[$size])
            : $this->getUrl();
    }

    public function getFormattedSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return $this->media_type === self::TYPE_IMAGE;
    }

    public function isVideo(): bool
    {
        return $this->media_type === self::TYPE_VIDEO;
    }

    public function isDocument(): bool
    {
        return $this->media_type === self::TYPE_DOCUMENT;
    }

    public function canGenerateThumbnails(): bool
    {
        return $this->isImage() && in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function generateThumbnails(): bool
    {
        if (!$this->canGenerateThumbnails()) {
            return false;
        }

        try {
            $originalPath = storage_path('app/public/' . $this->file_path);
            $thumbnails = [];
            
            $sizes = [
                'small' => [150, 150],
                'medium' => [300, 300],
                'large' => [800, 800],
            ];

            foreach ($sizes as $sizeName => [$width, $height]) {
                $thumbnailPath = $this->folder . '/thumbnails/' . $sizeName . '_' . $this->filename;
                $fullThumbnailPath = storage_path('app/public/' . $thumbnailPath);
                
                // Create directory if it doesn't exist
                $directory = dirname($fullThumbnailPath);
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Generate thumbnail
                $image = Image::make($originalPath);
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image->save($fullThumbnailPath, 90);
                
                $thumbnails[$sizeName] = $thumbnailPath;
            }

            $this->update([
                'thumbnails' => $thumbnails,
                'is_optimized' => true,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to generate thumbnails for media ID ' . $this->id . ': ' . $e->getMessage());
            return false;
        }
    }

    public function optimize(): bool
    {
        if (!$this->isImage() || $this->is_optimized) {
            return false;
        }

        try {
            $originalPath = storage_path('app/public/' . $this->file_path);
            
            // Optimize the original image
            $image = Image::make($originalPath);
            
            // Resize if too large
            if ($image->width() > 1920 || $image->height() > 1080) {
                $image->resize(1920, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Save with compression
            $image->save($originalPath, 85);
            
            // Generate thumbnails
            $this->generateThumbnails();
            
            // Update file size
            $newSize = filesize($originalPath);
            $this->update([
                'file_size' => $newSize,
                'is_optimized' => true,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to optimize media ID ' . $this->id . ': ' . $e->getMessage());
            return false;
        }
    }

    public function deleteFiles(): void
    {
        // Delete main file
        Storage::disk('public')->delete($this->file_path);
        
        // Delete thumbnails
        if ($this->thumbnails) {
            foreach ($this->thumbnails as $thumbnailPath) {
                Storage::disk('public')->delete($thumbnailPath);
            }
        }
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->tags ?: [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?: [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        $this->update(['tags' => array_values($tags)]);
    }

    public function getIconClass(): string
    {
        return match ($this->media_type) {
            self::TYPE_IMAGE => 'fas fa-image text-blue-500',
            self::TYPE_VIDEO => 'fas fa-video text-red-500',
            self::TYPE_AUDIO => 'fas fa-music text-purple-500',
            self::TYPE_DOCUMENT => 'fas fa-file-alt text-green-500',
            default => 'fas fa-file text-gray-500',
        };
    }

    public static function getMediaTypeOptions(): array
    {
        return [
            self::TYPE_IMAGE => 'Images',
            self::TYPE_VIDEO => 'Videos',
            self::TYPE_AUDIO => 'Audio',
            self::TYPE_DOCUMENT => 'Documents',
            self::TYPE_OTHER => 'Other',
        ];
    }

    // Static methods for file handling
    public static function uploadFile($file, $folder = 'uploads', $optimize = true): self
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($folder, $filename, 'public');
        
        $media = self::create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'folder' => $folder,
        ]);

        // Get image dimensions if it's an image
        if ($media->isImage()) {
            try {
                $imagePath = storage_path('app/public/' . $filePath);
                $imageSize = getimagesize($imagePath);
                $media->update([
                    'width' => $imageSize[0] ?? null,
                    'height' => $imageSize[1] ?? null,
                ]);

                if ($optimize) {
                    $media->optimize();
                }
            } catch (\Exception $e) {
                \Log::error('Failed to get image dimensions: ' . $e->getMessage());
            }
        }

        return $media;
    }

    public static function createFromUrl(string $url, string $folder = 'uploads'): ?self
    {
        try {
            $contents = file_get_contents($url);
            $filename = basename(parse_url($url, PHP_URL_PATH)) ?: 'downloaded_' . time();
            $tempFile = tempnam(sys_get_temp_dir(), 'media_');
            
            file_put_contents($tempFile, $contents);
            
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                $filename,
                mime_content_type($tempFile),
                null,
                true
            );
            
            $media = self::uploadFile($uploadedFile, $folder);
            
            unlink($tempFile);
            
            return $media;
        } catch (\Exception $e) {
            \Log::error('Failed to create media from URL: ' . $e->getMessage());
            return null;
        }
    }
}