<?php
// ============================================================================
// IMPROVED Certificate.php - Model Enhancements
// ============================================================================

namespace App\Models\Credentials;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Services\CertificateService;
use App\Models\Core\User;
use App\Models\Learning\Course;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_REQUESTED = 'requested';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'user_id', 'course_id', 'certificate_number', 'verification_code',
        'status', 'requested_at', 'approved_at', 'approved_by',
        'rejected_at', 'rejected_by', 'rejection_reason',
        'revoked_at', 'revoked_by', 'revocation_reason',
        'issued_date', 'completion_date', 'grade', 'credits',
        'certificate_template', 'metadata', 'verification_url',
        'qr_code_path', 'pdf_path',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'revoked_at' => 'datetime',
        'issued_date' => 'datetime',
        'completion_date' => 'datetime',
        'metadata' => 'array',
    ];

    // ========== RELATIONSHIPS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function revoker()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    // ========== BOOT & INITIALIZATION ==========
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->certificate_number = $model->certificate_number ?: $model->generateCertificateNumber();
            $model->verification_code = $model->verification_code ?: $model->generateVerificationCode();
            $model->verification_url = route('certificate.verify.code', $model->verification_code);
        });

        static::deleting(function ($model) {
            app(CertificateService::class)->cleanupAssets($model);
        });
    }

    // ========== GENERATION METHODS ==========
    public function generateCertificateNumber(): string
    {
        $year = now()->year;
        $courseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->course->title ?? 'CERT'), 0, 3));
        $sequence = str_pad($this->getNextSequenceNumber(), 4, '0', STR_PAD_LEFT);
        
        return "CERT-{$year}-{$courseCode}-{$sequence}";
    }

    public function generateVerificationCode(): string
    {
        return strtoupper(str_replace('-', '', Str::uuid()));
    }

    private function getNextSequenceNumber(): int
    {
        $currentYear = now()->year;
        $lastNumber = static::whereYear('created_at', $currentYear)
            ->orderBy('created_at', 'desc')
            ->first()
            ?->id ?? 0;
        
        return $lastNumber + 1;
    }

    // ========== STATUS CHECKS ==========
    public function isRequested(): bool { return $this->status === self::STATUS_REQUESTED; }
    public function isPending(): bool { return $this->status === self::STATUS_PENDING; }
    public function isApproved(): bool { return $this->status === self::STATUS_APPROVED; }
    public function isRejected(): bool { return $this->status === self::STATUS_REJECTED; }
    public function isRevoked(): bool { return $this->status === self::STATUS_REVOKED; }
    public function isActive(): bool { return $this->isApproved() && !$this->revoked_at; }

    // ========== STATE TRANSITIONS ==========
    public function approve(int $approverId = null): self
    {
        if (!$this->canBeApproved()) {
            throw new \Exception("Certificate cannot be approved (status: {$this->status})");
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $approverId ?? auth()->id(),
            'issued_date' => now(),
        ]);

        // Generate assets
        app(CertificateService::class)->generateCertificateAssets($this);
        
        // Notify student
        if (config('certificate.notifications.enabled', true)) {
            $this->user->notify(new \App\Notifications\CertificateApproved($this));
        }
        
        return $this->refresh();
    }

    public function reject(string $reason, int $rejecterId = null): self
    {
        if (!$this->canBeRejected()) {
            throw new \Exception("Certificate cannot be rejected (status: {$this->status})");
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejected_by' => $rejecterId ?? auth()->id(),
            'rejection_reason' => $reason,
        ]);

        if (config('certificate.notifications.enabled', true)) {
            $this->user->notify(new \App\Notifications\CertificateRejected($this));
        }
        
        return $this->refresh();
    }

    public function revoke(string $reason, int $revokerId = null): self
    {
        if (!$this->canBeRevoked()) {
            throw new \Exception("Certificate cannot be revoked (status: {$this->status})");
        }

        $this->update([
            'status' => self::STATUS_REVOKED,
            'revoked_at' => now(),
            'revoked_by' => $revokerId ?? auth()->id(),
            'revocation_reason' => $reason,
        ]);

        if (config('certificate.notifications.enabled', true)) {
            $this->user->notify(new \App\Notifications\CertificateRevoked($this));
        }
        
        return $this->refresh();
    }

    // ========== VERIFICATION ==========
    public static function findByVerificationCode(string $code): ?self
    {
        return static::where('verification_code', strtoupper(trim($code)))->first();
    }

    public function getVerificationData(): array
    {
        if (!$this->isActive()) {
            return [
                'valid' => false,
                'message' => $this->isRevoked() 
                    ? 'This certificate has been revoked.'
                    : 'This certificate is not valid.',
                'certificate' => null
            ];
        }

        return [
            'valid' => true,
            'message' => 'Certificate is valid and authentic.',
            'certificate' => [
                'id' => $this->id,
                'certificate_number' => $this->certificate_number,
                'student_name' => $this->user->name,
                'course_title' => $this->course->title,
                'instructor_name' => $this->course->instructor->name ?? 'N/A',
                'completion_date' => $this->completion_date->format('F j, Y'),
                'issued_date' => $this->issued_date?->format('F j, Y'),
                'grade' => $this->grade ?? 'Pass',
                'credits' => $this->credits,
                'verification_code' => $this->verification_code,
                'verification_url' => $this->verification_url,
            ]
        ];
    }

    // ========== PERMISSIONS ==========
    public function canBeApproved(): bool
    {
        return in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_PENDING]);
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_PENDING]);
    }

    public function canBeRevoked(): bool
    {
        return $this->isApproved() && !$this->revoked_at;
    }

    // ========== ACCESSORS ==========
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_REVOKED => 'gray',
            default => 'yellow',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'fas fa-check-circle',
            self::STATUS_REQUESTED => 'fas fa-clock',
            self::STATUS_REJECTED => 'fas fa-times-circle',
            self::STATUS_REVOKED => 'fas fa-ban',
            default => 'fas fa-certificate',
        };
    }

    // ========== SCOPES ==========
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_APPROVED)->whereNull('revoked_at');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRequested($query)
    {
        return $query->where('status', self::STATUS_REQUESTED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', self::STATUS_REVOKED);
    }

    public function scopeByInstructor($query, int $instructorId)
    {
        return $query->whereHas('course', fn($q) => $q->where('instructor_id', $instructorId));
    }

    public function scopeRecentlyCreated($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}