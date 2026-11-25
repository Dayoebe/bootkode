<?php

namespace App\Models\Career;

use Illuminate\Database\Eloquent\Model;

class CompetencyEvidence extends Model
{
    protected $table = 'competency_evidence';

    protected $guarded = ['id'];

    protected $hidden = ['review_history', 'reviewed_by', 'claim_hash'];

    protected $casts = [
        'review_history' => 'array', 'reviewed_at' => 'datetime', 'expires_at' => 'datetime', 'years_experience' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\Core\User::class);
    }
}
