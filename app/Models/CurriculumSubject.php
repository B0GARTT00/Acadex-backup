<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_id',
        'subject_code',
        'subject_description',
        'year_level',
        'semester',
        'is_deleted',
        'is_universal',
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }
}
