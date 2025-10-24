<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User; // UPDATED

class BlogReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reactable_type',
        'reactable_id',
        'user_id',
        'ip_address',
        'type'
    ];

    public function reactable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public static function toggle($reactableType, $reactableId, $type = 'like', $userId = null, $ipAddress = null)
    {
        $conditions = [
            'reactable_type' => $reactableType,
            'reactable_id' => $reactableId,
            'type' => $type,
        ];

        if ($userId) {
            $conditions['user_id'] = $userId;
        } else {
            $conditions['ip_address'] = $ipAddress;
        }

        $reaction = static::where($conditions)->first();

        if ($reaction) {
            $reaction->delete();
            return false; // Removed
        } else {
            static::create($conditions);
            return true; // Added
        }
    }
    public function commentAuthor()
{
    return $this->through('reactable')->has('user');
}

public function postAuthor()
{
    return $this->through('reactable')->has('author');
}
}
