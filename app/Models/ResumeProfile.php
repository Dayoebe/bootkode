<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ResumeProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'professional_title',
        'email',
        'phone',
        'location',
        'website',
        'linkedin',
        'github',
        'professional_summary',
        'profile_image',
        'work_experience',
        'education',
        'skills',
        'projects',
        'certifications',
        'languages',
        'references',
        'custom_sections',
        'selected_template',
        'color_scheme',
        'font_family',
        'section_order',
        'section_visibility',
        'show_profile_image',
        'is_public',
        'public_slug',
        'is_premium',
        'last_edited_at',
        'view_count',
        'download_count',
        'last_downloaded_at',
    ];

    protected $casts = [
        'work_experience' => 'array',
        'education' => 'array',
        'skills' => 'array',
        'projects' => 'array',
        'certifications' => 'array',
        'languages' => 'array',
        'references' => 'array',
        'custom_sections' => 'array',
        'section_order' => 'array',
        'section_visibility' => 'array',
        'show_profile_image' => 'boolean',
        'is_public' => 'boolean',
        'is_premium' => 'boolean',
        'last_edited_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Boot method for model events
    protected static function booted()
    {
        static::creating(function ($resume) {
            $resume->public_slug = $resume->generateUniqueSlug();
            $resume->last_edited_at = now();
        });

        static::updating(function ($resume) {
            $resume->last_edited_at = now();
        });
    }

    // Accessors & Mutators
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return Storage::url($this->profile_image);
        }
        return null;
    }

    public function getCompletionPercentageAttribute()
    {
        $sections = [
            'full_name',
            'professional_title',
            'email',
            'professional_summary',
            'work_experience',
            'education',
            'skills'
        ];

        $completed = 0;
        $total = count($sections);

        foreach ($sections as $section) {
            if ($section === 'work_experience' || $section === 'education' || $section === 'skills') {
                if (!empty($this->{$section})) {
                    $completed++;
                }
            } else {
                if (!empty($this->{$section})) {
                    $completed++;
                }
            }
        }

        return round(($completed / $total) * 100);
    }

    // Helper Methods
    public function generateUniqueSlug()
    {
        $baseSlug = Str::slug($this->full_name ?? $this->user->name ?? 'resume');
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('public_slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function getDefaultSectionOrder()
    {
        return [
            'personal_info',
            'professional_summary',
            'work_experience',
            'education',
            'skills',
            'projects',
            'certifications',
            'languages',
            'references'
        ];
    }

    public function getSectionOrderAttribute($value)
    {
        return $value ? json_decode($value, true) : $this->getDefaultSectionOrder();
    }

    public function getDefaultSectionVisibility()
    {
        return [
            'personal_info' => true,
            'professional_summary' => true,
            'work_experience' => true,
            'education' => true,
            'skills' => true,
            'projects' => true,
            'certifications' => true,
            'languages' => false,
            'references' => false
        ];
    }

    public function getSectionVisibilityAttribute($value)
    {
        $default = $this->getDefaultSectionVisibility();
        return $value ? array_merge($default, json_decode($value, true)) : $default;
    }

    // Template & Styling Methods
    public function getAvailableTemplates()
    {
        return [
            'modern' => [
                'name' => 'Modern Professional',
                'description' => 'Clean, minimalist design with sidebar',
                'preview' => 'modern-preview.jpg',
                'is_premium' => false
            ],
            'classic' => [
                'name' => 'Classic Traditional',
                'description' => 'Traditional format with centered header',
                'preview' => 'classic-preview.jpg',
                'is_premium' => false
            ],
            'creative' => [
                'name' => 'Creative Design',
                'description' => 'Bold colors and creative layout',
                'preview' => 'creative-preview.jpg',
                'is_premium' => true
            ],
            'executive' => [
                'name' => 'Executive Premium',
                'description' => 'Sophisticated design for senior professionals',
                'preview' => 'executive-preview.jpg',
                'is_premium' => true
            ],
            'minimal' => [
                'name' => 'Minimal Clean',
                'description' => 'Ultra-clean design with perfect spacing',
                'preview' => 'minimal-preview.jpg',
                'is_premium' => true
            ]
        ];
    }

    public function getAvailableColorSchemes()
    {
        return [
            'professional' => ['name' => 'Professional Blue', 'primary' => '#2563eb', 'secondary' => '#64748b'],
            'elegant' => ['name' => 'Elegant Navy', 'primary' => '#1e293b', 'secondary' => '#475569'],
            'modern' => ['name' => 'Modern Purple', 'primary' => '#7c3aed', 'secondary' => '#6b7280'],
            'creative' => ['name' => 'Creative Orange', 'primary' => '#ea580c', 'secondary' => '#78716c'],
            'minimal' => ['name' => 'Minimal Gray', 'primary' => '#374151', 'secondary' => '#9ca3af'],
        ];
    }

    public function getAvailableFonts()
    {
        return [
            'inter' => 'Inter (Modern Sans-serif)',
            'roboto' => 'Roboto (Clean & Professional)',
            'open-sans' => 'Open Sans (Friendly & Readable)',
            'lato' => 'Lato (Corporate Style)',
            'merriweather' => 'Merriweather (Elegant Serif)',
            'source-sans' => 'Source Sans Pro (Technical)'
        ];
    }

    // Data Management Methods
    public function addWorkExperience($data)
    {
        $experiences = $this->work_experience ?? [];
        $experiences[] = array_merge($data, ['id' => Str::uuid()]);
        $this->work_experience = $experiences;
        return $this;
    }

    public function updateWorkExperience($id, $data)
    {
        $experiences = $this->work_experience ?? [];
        foreach ($experiences as $key => $exp) {
            if ($exp['id'] === $id) {
                $experiences[$key] = array_merge($exp, $data);
                break;
            }
        }
        $this->work_experience = $experiences;
        return $this;
    }

    public function removeWorkExperience($id)
    {
        $experiences = $this->work_experience ?? [];
        $this->work_experience = array_filter($experiences, fn($exp) => $exp['id'] !== $id);
        return $this;
    }

    public function addEducation($data)
    {
        $education = $this->education ?? [];
        $education[] = array_merge($data, ['id' => Str::uuid()]);
        $this->education = $education;
        return $this;
    }

    public function addSkill($data)
    {
        $skills = $this->skills ?? [];
        $skills[] = array_merge($data, ['id' => Str::uuid()]);
        $this->skills = $skills;
        return $this;
    }

    public function addProject($data)
    {
        $projects = $this->projects ?? [];
        $projects[] = array_merge($data, ['id' => Str::uuid()]);
        $this->projects = $projects;
        return $this;
    }

    // Analytics & Tracking
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }

    // Export Methods
    public function canBeExported()
    {
        return !empty($this->full_name) && !empty($this->professional_title);
    }

    public function generateFilename($format = 'pdf')
    {
        $name = Str::slug($this->full_name ?? 'resume');
        $timestamp = now()->format('Y-m-d');
        return "{$name}-resume-{$timestamp}.{$format}";
    }

    // Validation & Quality Check
    public function getQualityScore()
    {
        $score = 0;
        $maxScore = 100;

        // Essential information (40 points)
        if ($this->full_name)
            $score += 10;
        if ($this->professional_title)
            $score += 10;
        if ($this->email)
            $score += 5;
        if ($this->phone)
            $score += 5;
        if ($this->professional_summary)
            $score += 10;

        // Work experience (25 points)
        $workExp = $this->work_experience ?? [];
        if (count($workExp) >= 1)
            $score += 15;
        if (count($workExp) >= 2)
            $score += 10;

        // Education (15 points)
        $education = $this->education ?? [];
        if (count($education) >= 1)
            $score += 15;

        // Skills (10 points)
        $skills = $this->skills ?? [];
        if (count($skills) >= 3)
            $score += 5;
        if (count($skills) >= 6)
            $score += 5;

        // Projects (10 points)
        $projects = $this->projects ?? [];
        if (count($projects) >= 1)
            $score += 5;
        if (count($projects) >= 2)
            $score += 5;

        return min($score, $maxScore);
    }

    public function getSuggestions()
    {
        $suggestions = [];

        if (empty($this->professional_summary)) {
            $suggestions[] = [
                'type' => 'critical',
                'title' => 'Add Professional Summary',
                'description' => 'A professional summary helps employers quickly understand your value proposition.'
            ];
        }

        $workExp = $this->work_experience ?? [];
        if (count($workExp) < 2) {
            $suggestions[] = [
                'type' => 'important',
                'title' => 'Add More Work Experience',
                'description' => 'Include at least 2-3 relevant work experiences to showcase your career progression.'
            ];
        }

        $skills = $this->skills ?? [];
        if (count($skills) < 5) {
            $suggestions[] = [
                'type' => 'improvement',
                'title' => 'Expand Skills Section',
                'description' => 'Add more technical and soft skills relevant to your target role.'
            ];
        }

        return $suggestions;
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('last_edited_at', 'desc');
    }
    
    
}