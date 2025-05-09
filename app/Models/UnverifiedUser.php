<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\Course;

class UnverifiedUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'department_id',
        'course_id',
        'is_universal',
    ];

    /**
     * Get the department associated with the unverified user.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the course associated with the unverified user.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
