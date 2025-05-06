<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;

class CoursesTableSeeder extends Seeder
{
    public function run(): void
    {
        $asbm = Department::where('department_code', 'ASBM')->first();
        $nursing = Department::where('department_code', 'NURSING')->first();

        if (!$asbm || !$nursing) {
            throw new \Exception('Required departments not found. Seed departments first.');
        }

        // Regular degree programs
        Course::create([
            'course_code' => 'BSIT',
            'course_description' => 'Bachelor of Science in Information Technology',
            'department_id' => $asbm->id,
        ]);

        Course::create([
            'course_code' => 'BSBA',
            'course_description' => 'Bachelor of Science in Business Administration',
            'department_id' => $asbm->id,
        ]);

        Course::create([
            'course_code' => 'BSN',
            'course_description' => 'Bachelor of Science in Nursing',
            'department_id' => $nursing->id,
        ]);

        // Add General Education course under ASBM
        Course::create([
            'course_code' => 'GE',
            'course_description' => 'General Education',
            'department_id' => $asbm->id,
        ]);
    }
}
