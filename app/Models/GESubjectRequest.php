<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Department;

class GESubjectRequest extends Model
{
    protected $table = 'ge_subject_requests';
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'department_id',
        'chairperson_id',
        'request_reason',
        'response_reason',
        'status'
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function chairperson()
    {
        return $this->belongsTo(User::class, 'chairperson_id');
    }
}
