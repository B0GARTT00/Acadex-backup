<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsTableSeeder extends Seeder
{
    public function run(): void
    {
        Department::create([
            'department_code' => 'ASBM',
            'department_description' => 'Arts, Science, and Business Management',
        ]);

        Department::create([
            'department_code' => 'NURSING',
            'department_description' => 'School of Nursing',
        ]);

        Department::create([
            'department_code' => 'Medicine',
            'department_description' => 'School of Medicine',
        ]);

        Department::create([
            'department_code' => 'ALLIED',
            'department_description' => 'Allied Health',
        ]);
    }
}
