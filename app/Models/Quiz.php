<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'module_id',
        'title',
        'description',
        'pass_percentage',
    ];

    /**
     * Get the lesson that the quiz belongs to (if applicable).
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the module that the quiz belongs to (if applicable).
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the questions for the quiz.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}