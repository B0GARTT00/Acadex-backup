<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'course_code',
        'course_description',
        'name',
        'department_id',
        'is_deleted',
        'created_by',
        'updated_by',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * Relationships
     */

    // 🔗 Department (Many Courses belong to one Department)
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // 🔗 Students (One Course has many Students)
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // 🔗 Subjects (One Course has many Subjects)
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Accessors
     */

    // Combined label accessor (ex: "BSIT - Bachelor of Science in Information Technology")
    public function getCourseLabelAttribute()
    {
        return "{$this->course_code} - {$this->course_description}";
    }
}
