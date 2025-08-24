<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\CertificateService;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_REQUESTED = 'requested';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'verification_code',
        'status',
        'requested_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'revoked_at',
        'revoked_by',
        'revocation_reason',
        'issued_date',
        'completion_date',
        'grade',
        'credits',
        'certificate_template',
        'metadata',
        'verification_url',
        'qr_code_path',
        'pdf_path',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = $certificate->generateCertificateNumber();
            }
            if (empty($certificate->verification_code)) {
                $certificate->verification_code = $certificate->generateVerificationCode();
            }
            if (empty($certificate->verification_url)) {
                $certificate->verification_url = route('certificate.verify.code', $certificate->verification_code);
            }
        });

        static::deleting(function ($certificate) {
            // Clean up associated files
            app(CertificateService::class)->cleanupAssets($certificate);
        });
    }

    // Relationships
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

    // Certificate Number Generation
    public function generateCertificateNumber()
    {
        $year = now()->year;
        $courseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->course->title ?? 'COURSE'), 0, 3));
        $sequence = str_pad($this->getNextSequenceNumber(), 4, '0', STR_PAD_LEFT);
        
        return "CERT-{$year}-{$courseCode}-{$sequence}";
    }

    // Verification Code Generation (UUID-based)
    public function generateVerificationCode()
    {
        return strtoupper(str_replace('-', '', Str::uuid()));
    }

    // Get next sequence number for the year
    private function getNextSequenceNumber()
    {
        $currentYear = now()->year;
        $lastCertificate = static::whereYear('created_at', $currentYear)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastCertificate) {
            return 1;
        }

        // Extract sequence number from certificate number
        $parts = explode('-', $lastCertificate->certificate_number);
        $lastSequence = (int) end($parts);
        
        return $lastSequence + 1;
    }

    // Status Methods
    public function isRequested()
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isRevoked()
    {
        return $this->status === self::STATUS_REVOKED;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_APPROVED && !$this->revoked_at;
    }

    // Action Methods
    public function approve($approverId = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $approverId ?? auth()->id(),
            'issued_date' => now(),
        ]);

        // Generate certificate PDF and QR code
        app(CertificateService::class)->generateCertificateAssets($this);
        
        // Send notification to student
        if (config('certificate.notifications.enabled', true)) {
            $this->user->notify(new \App\Notifications\CertificateApproved($this));
        }
        
        return $this;
    }

    public function reject($rejectionReason, $rejecterId = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejected_by' => $rejecterId ?? auth()->id(),
            'rejection_reason' => $rejectionReason,
        ]);

        // Send notification to student
        if (config('certificate.notifications.enabled', true)) {
            $this->user->notify(new \App\Notifications\CertificateRejected($this));
        }
        
        return $this;
    }

    public function revoke($revocationReason, $revokerId = null)
    {
        $this->update([
            'status' => self::STATUS_REVOKED,
            'revoked_at' => now(),
            'revoked_by' => $revokerId ?? auth()->id(),
            'revocation_reason' => $revocationReason,
        ]);

        // Send notification to student
        if (config('certificate.notifications.enabled', true)) {
            $this->user->notify(new \App\Notifications\CertificateRevoked($this));
        }
        
        return $this;
    }

    // Verification Methods
    public static function findByVerificationCode($code)
    {
        return static::where('verification_code', strtoupper(trim($code)))->first();
    }

    public function getVerificationData()
    {
        if (!$this->isActive()) {
            return [
                'valid' => false,
                'message' => $this->isRevoked() 
                    ? 'This certificate has been revoked and is no longer valid.'
                    : 'This certificate is not valid.',
                'certificate' => null
            ];
        }

        return [
            'valid' => true,
            'message' => 'Certificate is valid and authentic.',
            'certificate' => [
                'certificate_number' => $this->certificate_number,
                'student_name' => $this->user->name,
                'course_title' => $this->course->title,
                'instructor_name' => $this->course->instructor->name ?? 'N/A',
                'completion_date' => $this->completion_date->format('F j, Y'),
                'issued_date' => $this->issued_date?->format('F j, Y') ?? 'N/A',
                'grade' => $this->grade ?? 'Pass',
                'credits' => $this->credits,
                'verification_code' => $this->verification_code,
                'verification_url' => $this->verification_url,
            ]
        ];
    }

    // Accessors
    public function getFormattedCertificateNumberAttribute()
    {
        return $this->certificate_number;
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_REQUESTED => 'blue',
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_REVOKED => 'gray',
            default => 'gray'
        };
    }

    public function getStatusIconAttribute()
    {
        return match ($this->status) {
            self::STATUS_REQUESTED => 'fas fa-clock',
            self::STATUS_PENDING => 'fas fa-hourglass-half',
            self::STATUS_APPROVED => 'fas fa-check-circle',
            self::STATUS_REJECTED => 'fas fa-times-circle',
            self::STATUS_REVOKED => 'fas fa-ban',
            default => 'fas fa-question-circle'
        };
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code_path 
            ? asset('storage/' . $this->qr_code_path)
            : null;
    }

    public function getPdfUrlAttribute()
    {
        return $this->pdf_path 
            ? route('certificate.download', $this->verification_code)
            : null;
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeRequested($query)
    {
        return $query->where('status', self::STATUS_REQUESTED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
                    ->whereNull('revoked_at');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', self::STATUS_REVOKED);
    }

    public function scopeRecentlyCreated($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->whereHas('course', function($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        });
    }

    // Helper Methods
    public function canBeApproved()
    {
        return in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_PENDING]);
    }

    public function canBeRejected()
    {
        return in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_PENDING]);
    }

    public function canBeRevoked()
    {
        return $this->status === self::STATUS_APPROVED && !$this->revoked_at;
    }

    public function getDaysToExpire()
    {
        // If your certificates have expiration dates
        if (!$this->expires_at) {
            return null;
        }
        
        return now()->diffInDays($this->expires_at, false);
    }

    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return now()->gt($this->expires_at);
    }
}