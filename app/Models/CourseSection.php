<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\CourseLesson;

class CourseSection extends Model
{ 
protected $fillable = ['course_id', 'title', 'description', 'order'];


// public function course()
// {
//     return $this->belongsTo(Course::class);
// }

// public function lessons()
// {
//     return $this->hasMany(CourseLesson::class)->orderBy('order');
// }
public function course()
{
    return $this->belongsTo(Course::class);
    // Should have course_id foreign key
}
public function lessons()
{
    return $this->hasMany(CourseLesson::class)->orderBy('order');
}
}