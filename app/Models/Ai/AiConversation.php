<?php

namespace App\Models\Ai;

use App\Models\Core\User;
use App\Models\Learning\Course;
use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    protected $fillable = ['user_id', 'course_id', 'title', 'last_message_at'];

    protected $casts = ['last_message_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function messages()
    {
        return $this->hasMany(AiConversationMessage::class, 'conversation_id');
    }
}
