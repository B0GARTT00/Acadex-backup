<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_code', 'department_description', 'department_name', 'is_deleted', 'created_by', 'updated_by'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
