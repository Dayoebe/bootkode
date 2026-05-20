<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Institution;
use App\Models\Core\User;

class InstitutionLicenseHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'action',
        'old_values',
        'new_values',
        'reason',
        'performed_by',
        'performed_at',
        'notes'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'performed_at' => 'datetime'
    ];

    const ACTIONS = [
        'created' => 'License Created',
        'activated' => 'License Activated',
        'renewed' => 'License Renewed',
        'upgraded' => 'License Upgraded',
        'downgraded' => 'License Downgraded',
        'suspended' => 'License Suspended',
        'cancelled' => 'License Cancelled',
        'expired' => 'License Expired'
    ];

    // Relationships
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Accessors
    public function getActionNameAttribute()
    {
        return self::ACTIONS[$this->action] ?? ucfirst($this->action);
    }
}
