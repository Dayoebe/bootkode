<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'background_image_path',
        'content_areas',
        'default_font',
        'default_font_size',
        'default_font_color',
        'is_active'
    ];

    protected $casts = [
        'content_areas' => 'array',
        'is_active' => 'boolean'
    ];

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'template_id');
    }
}