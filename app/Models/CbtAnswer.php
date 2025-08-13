<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_exam_id',
        'cbt_question_id',
        'user_id',
        'selected_option_index',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(CbtExam::class);
    }

    public function question()
    {
        return $this->belongsTo(CbtQuestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}