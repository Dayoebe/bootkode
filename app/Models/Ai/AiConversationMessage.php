<?php

namespace App\Models\Ai;

use Illuminate\Database\Eloquent\Model;

class AiConversationMessage extends Model
{
    protected $fillable = ['conversation_id', 'role', 'content', 'context', 'metadata'];

    protected $casts = ['context' => 'array', 'metadata' => 'array'];

    public function conversation()
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }
}
