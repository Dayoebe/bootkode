<?php

namespace App\Models\Mentorship;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeReviewRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_review_id',
        'user_id',
        'revision_number',
        'repository_url',
        'branch_name',
        'pull_request_url',
        'commit_hash',
        'files_to_review',
        'language',
        'code_snippet',
        'learner_goal',
        'notes',
        'submitted_at',
    ];

    protected $casts = [
        'files_to_review' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function codeReview()
    {
        return $this->belongsTo(CodeReview::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(CodeReviewComment::class, 'code_review_revision_id');
    }
}
