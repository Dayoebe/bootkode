<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    // In OfflineNote.php
public function scopeForCourse($query, $courseId)
{
    return $query->where('course_id', $courseId);
}

public function scopeGeneralNotes($query)
{
    return $query->whereNull('course_id');
}
// In OfflineNote.php
protected $appends = ['is_general_note'];

public function getIsGeneralNoteAttribute()
{
    return is_null($this->course_id);
}
}