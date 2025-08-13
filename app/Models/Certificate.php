<?php

namespace App\Models;

use App\Notifications\CertificateUpdateNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'template_id',
        'uuid',
        'verification_code',
        'issue_date',
        'expiry_date',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
        'status' => CertificateStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($certificate) {
            if ($certificate->isDirty('status')) {
                $certificate->user->notify(new CertificateUpdateNotification($certificate));
                $certificate->user->logCustomActivity('Certificate status updated to ' . $certificate->status, ['certificate_id' => $certificate->id]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}