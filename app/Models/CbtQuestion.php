<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_exam_id',
        'question',
        'options',
        'correct_option_index',
        'marks',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function exam()
    {
        return $this->belongsTo(CbtExam::class);
    }

    public function answers()
    {
        return $this->hasMany(CbtAnswer::class);
    }
}