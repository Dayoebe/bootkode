<?php

namespace App\Traits;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

trait HasCloudinaryUpload
{
    /**
     * Get Cloudinary instance
     *
     * @return Cloudinary
     */

     protected function getCloudinaryInstance(): Cloudinary
{
    // This automatically reads CLOUDINARY_URL from .env
    return new Cloudinary(); 
    // YES! Just `new Cloudinary()` — it auto-detects CLOUDINARY_URL
}
    // protected function getCloudinaryInstance(): Cloudinary
    // {
    //     return new Cloudinary([
    //         'cloud' => [
    //             'cloud_name' => config('cloudinary.cloud_name'),
    //             'api_key' => config('cloudinary.api_key'),
    //             'api_secret' => config('cloudinary.api_secret'),
    //         ],
    //         'url' => [
    //             'secure' => true
    //         ]
    //     ]);
    // }

    /**
     * Get Upload API instance
     *
     * @return UploadApi
     */
    protected function getUploadApi(): UploadApi
    {
        $cloudinary = $this->getCloudinaryInstance();
        return $cloudinary->uploadApi();
    }

    /**
     * Upload file to Cloudinary (simple method)
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array|null
     */
    public function uploadToCloudinary(UploadedFile $file, string $folder = 'uploads', array $options = []): ?array
    {
        try {
            $uploadApi = $this->getUploadApi();
            
            $defaultOptions = [
                'folder' => $folder,
                'resource_type' => 'auto',
            ];

            $uploadOptions = array_merge($defaultOptions, $options);
            
            $result = $uploadApi->upload($file->getRealPath(), $uploadOptions);

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'],
                'format' => $result['format'],
                'width' => $result['width'] ?? null,
                'height' => $result['height'] ?? null,
                'resource_type' => $result['resource_type'],
                'bytes' => $result['bytes'] ?? null,
                'created_at' => $result['created_at'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed', [
                'folder' => $folder,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Upload profile picture to Cloudinary with transformations
     *
     * @param UploadedFile $file
     * @param string|int|null $userId
     * @return array|null
     */
    public function uploadProfilePicture(UploadedFile $file, $userId = null): ?array
    {
        $userId = $userId ?? auth()->id();
        
        try {
            $uploadApi = $this->getUploadApi();
            
            $result = $uploadApi->upload($file->getRealPath(), [
                'folder' => 'profile-pictures',
                'public_id' => "user_{$userId}_" . time(),
                'transformation' => [
                    'width' => 400,
                    'height' => 400,
                    'crop' => 'fill',
                    'gravity' => 'face',
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ],
                'tags' => ['profile_picture', "user_{$userId}"],
                'resource_type' => 'image',
                'overwrite' => true,
            ]);

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'],
                'format' => $result['format'],
                'width' => $result['width'] ?? null,
                'height' => $result['height'] ?? null,
                'resource_type' => $result['resource_type'],
            ];
        } catch (\Exception $e) {
            Log::error('Profile picture upload failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Upload file with custom transformations
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $publicId
     * @param array $transformations
     * @return array|null
     */
    public function uploadWithTransformations(
        UploadedFile $file, 
        string $folder, 
        string $publicId, 
        array $transformations = []
    ): ?array
    {
        try {
            $uploadApi = $this->getUploadApi();
            
            $options = [
                'folder' => $folder,
                'public_id' => $publicId,
                'resource_type' => 'auto',
                'overwrite' => true,
            ];

            if (!empty($transformations)) {
                $options['transformation'] = $transformations;
            }

            $result = $uploadApi->upload($file->getRealPath(), $options);

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'],
                'format' => $result['format'],
                'width' => $result['width'] ?? null,
                'height' => $result['height'] ?? null,
                'resource_type' => $result['resource_type'],
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload with transformations failed', [
                'folder' => $folder,
                'public_id' => $publicId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Delete file from Cloudinary
     *
     * @param string $publicId
     * @return bool
     */
    public function deleteFromCloudinary(string $publicId): bool
    {
        try {
            $cloudinary = $this->getCloudinaryInstance();
            $cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            Log::error('Cloudinary deletion failed', [
                'public_id' => $publicId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Extract public_id from Cloudinary URL
     *
     * @param string $url
     * @return string|null
     */
    public function extractPublicId(string $url): ?string
    {
        // Extract public_id from Cloudinary URL
        // Example: https://res.cloudinary.com/bootkode/image/upload/v1234567890/profile-pictures/user_1_1234567890.jpg
        preg_match('/\/upload\/(?:v\d+\/)?(.+)\.[^.]+$/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Get optimized image URL
     *
     * @param string $publicId
     * @param array $transformations
     * @return string
     */
    public function getOptimizedUrl(string $publicId, array $transformations = []): string
    {
        try {
            $cloudinary = $this->getCloudinaryInstance();
            
            $defaultTransformations = [
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ];

            $options = array_merge($defaultTransformations, $transformations);
            
            return $cloudinary->image($publicId)->toUrl($options);
        } catch (\Exception $e) {
            Log::error('Failed to get optimized URL', [
                'public_id' => $publicId,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Get thumbnail URL
     *
     * @param string $publicId
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getThumbnailUrl(string $publicId, int $width = 150, int $height = 150): string
    {
        return $this->getOptimizedUrl($publicId, [
            'width' => $width,
            'height' => $height,
            'crop' => 'fill',
            'gravity' => 'face'
        ]);
    }

    /**
     * Upload video to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return array|null
     */
    public function uploadVideo(UploadedFile $file, string $folder = 'videos'): ?array
    {
        try {
            $uploadApi = $this->getUploadApi();
            
            $result = $uploadApi->upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'video',
                'quality' => 'auto',
            ]);

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'],
                'format' => $result['format'],
                'resource_type' => $result['resource_type'],
                'duration' => $result['duration'] ?? null,
                'bytes' => $result['bytes'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Video upload failed', [
                'folder' => $folder,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Upload cover image (wider dimensions)
     *
     * @param UploadedFile $file
     * @param string|int|null $userId
     * @return array|null
     */
    public function uploadCoverImage(UploadedFile $file, $userId = null): ?array
    {
        $userId = $userId ?? auth()->id();
        
        try {
            $uploadApi = $this->getUploadApi();
            
            $result = $uploadApi->upload($file->getRealPath(), [
                'folder' => 'cover-images',
                'public_id' => "cover_{$userId}_" . time(),
                'transformation' => [
                    'width' => 1200,
                    'height' => 400,
                    'crop' => 'fill',
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ],
                'tags' => ['cover_image', "user_{$userId}"],
                'resource_type' => 'image',
                'overwrite' => true,
            ]);

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'],
                'format' => $result['format'],
                'width' => $result['width'] ?? null,
                'height' => $result['height'] ?? null,
                'resource_type' => $result['resource_type'],
            ];
        } catch (\Exception $e) {
            Log::error('Cover image upload failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Batch upload files
     *
     * @param array $files Array of UploadedFile objects
     * @param string $folder
     * @return array
     */
    public function batchUpload(array $files, string $folder = 'uploads'): array
    {
        $results = [];
        
        foreach ($files as $key => $file) {
            if ($file instanceof UploadedFile) {
                $result = $this->uploadToCloudinary($file, $folder);
                $results[$key] = $result;
            }
        }
        
        return $results;
    }

    /**
     * Upload raw file (PDFs, documents, etc.)
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return array|null
     */
    public function uploadRawFile(UploadedFile $file, string $folder = 'documents'): ?array
    {
        try {
            $uploadApi = $this->getUploadApi();
            
            $result = $uploadApi->upload($file->getRealPath(), [
                'folder' => $folder,
                'resource_type' => 'raw',
            ]);

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'],
                'format' => $result['format'],
                'resource_type' => $result['resource_type'],
                'bytes' => $result['bytes'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Raw file upload failed', [
                'folder' => $folder,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}