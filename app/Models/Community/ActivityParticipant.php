<?php 
// app/Models/Community/ActivityParticipant.php
namespace App\Models\Community;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class ActivityParticipant extends Model
{
    use HasFactory;

    protected $table = 'activity_participants';

    protected $fillable = [
        'activity_id', 'user_id', 'status', 'submission_data', 'score', 
        'joined_at', 'completed_at'
    ];

    protected $casts = [
        'submission_data' => 'array',
        'joined_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(CommunityActivity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complete($score = null, $submissionData = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'score' => $score,
            'submission_data' => $submissionData,
        ]);
    }

    public function scopeCompleted(Builder $query)
    {
        return $query->where('status', 'completed');
    }
}
