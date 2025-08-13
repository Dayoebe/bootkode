<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_exam_id',
        'user_id',
        'score',
        'total_marks',
        'passed',
        'completed_at',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(CbtExam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}