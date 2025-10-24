<?php

namespace App\Models\Mentorship;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\User;
class CodeSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id', 'user_id', 'code', 'language', 
        'status', 'feedback', 'score', 'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function challenge()
    {
        return $this->belongsTo(CodeChallenge::class, 'challenge_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
