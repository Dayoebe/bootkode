<?php

namespace App\Models\Mentorship;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeReviewComment extends Model
{
    use HasFactory;

    public const TYPE_COMMENT = 'comment';
    public const TYPE_RUBRIC = 'rubric';
    public const TYPE_REVISION_REQUEST = 'revision_request';
    public const TYPE_APPROVAL = 'approval';

    protected $fillable = [
        'code_review_id',
        'code_review_revision_id',
        'user_id',
        'type',
        'rubric_key',
        'visibility',
        'body',
        'metadata',
        'resolved_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function codeReview()
    {
        return $this->belongsTo(CodeReview::class);
    }

    public function revision()
    {
        return $this->belongsTo(CodeReviewRevision::class, 'code_review_revision_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
