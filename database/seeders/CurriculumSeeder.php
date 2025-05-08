<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use App\Models\Course;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        // Get the BSIT course or create if missing
        $bsit = Course::firstOrCreate(
            ['course_code' => 'BSIT'],
            ['course_description' => 'Bachelor of Science in Information Technology', 'department_id' => 1]
        );

        // Create the curriculum
        $curriculum = Curriculum::firstOrCreate([
            'course_id' => $bsit->id,
            'name' => '2022 Curriculum',
        ]);

        // Curriculum subject entries
        $subjects = [
            [1, '1st', 'GE 1', 'Understanding the Self'],
            [1, '1st', 'GE 4', 'Mathematics in the Modern World'],
            [1, '1st', 'GE 5', 'Purposive Communication'],
            [1, '1st', 'IT 101', 'Introduction to Computing'],
            [1, '1st', 'IT 102', 'Computer Programming 1'],
            [1, '1st', 'NSTP 1', 'Civic Welfare Training Service 1 (CWTS1)'],
            [1, '1st', 'PD 1', 'Academic & Personal Adjustment'],
            [1, '1st', 'PE 1', 'Movement Enhancement'],
            [1, '1st', 'RS 1', 'Message & Teaching of Old Testament'],

            [1, '2nd', 'GE 2', 'Readings in Philippine History'],
            [1, '2nd', 'GE 7', 'Science, Technology and Society'],
            [1, '2nd', 'IT 103', 'Computer Programming 2'],
            [1, '2nd', 'IT 104', 'Introduction to Human Computer Interaction'],
            [1, '2nd', 'IT 105', 'Discrete Mathematics'],
            [1, '2nd', 'NSTP 2', 'Civic Welfare Training Service 2 (CWTS 2)'],
            [1, '2nd', 'PD 2', 'Self-Awareness and Self-Management'],
            [1, '2nd', 'PE 2', 'Fitness Exercises'],
            [1, '2nd', 'RS 2', 'Message and Teachings of New Testament'],

            [2, '1st', 'GE 11', 'Living in the IT Era'],
            [2, '1st', 'GE 9', 'Life and Works of Rizal'],
            [2, '1st', 'IT 1', 'IT Elective 1'],
            [2, '1st', 'IT 201', 'Data Structure and Algorithm'],
            [2, '1st', 'IT 202', 'Computer Organization & Architecture'],
            [2, '1st', 'IT 203', 'Accounting for IT'],
            [2, '1st', 'PD 3', 'Values Development & Interpersonal Relationship'],
            [2, '1st', 'PE 3', 'Dance for Theatre'],

            [2, '2nd', 'GE 12', "People and Earth's Ecosystem"],
            [2, '2nd', 'GE 3', 'The Contemporary World'],
            [2, '2nd', 'IT 2', 'IT Elective 2'],
            [2, '2nd', 'IT 204', 'Information Management'],
            [2, '2nd', 'IT 205', 'Integrative Programming and Technologies 1'],
            [2, '2nd', 'IT 206', 'Networking 1'],
            [2, '2nd', 'PD 4', 'Career Development & Community Involvement'],
            [2, '2nd', 'PE 4', 'Sports Activities (Individual, Dual and Team Sports)'],

            [3, '1st', 'GE 6', 'Art Appreciation'],
            [3, '1st', 'IT 3', 'IT Elective 3'],
            [3, '1st', 'IT 301', 'Advanced Database System'],
            [3, '1st', 'IT 302', 'System Integration and Architecture 1'],
            [3, '1st', 'IT 303', 'Networking 2'],
            [3, '1st', 'IT 304', 'Quantitative Methods (w/Modeling and Simulation)'],
            [3, '1st', 'IT 305', 'Social and Professional Issues in IT'],

            [3, '2nd', 'GE 10', 'The Entrepreneurial Mind'],
            [3, '2nd', 'GE 8', 'Ethics'],
            [3, '2nd', 'IT 306', 'Applications Development & Emerging Tech'],
            [3, '2nd', 'IT 307', 'Information and Assurance and Security 1'],
            [3, '2nd', 'IT 308', 'Web Frameworks'],
            [3, '2nd', 'IT 309', 'Project Management'],
            [3, '2nd', 'IT 4', 'IT Elective 4'],

            [4, '1st', 'IT 400', 'Practicum (486 hours)'],
            [4, '1st', 'IT 401', 'Capstone Project 1'],
            [4, '1st', 'IT 402', 'Mobile Application Development'],
            [4, '1st', 'IT 403', 'Information Assurance and Security 2'],

            [4, '2nd', 'IT 404', 'Capstone Project 2'],
            [4, '2nd', 'IT 405', 'Systems Administration and Maintenance'],
            [4, '2nd', 'IT 406', 'Emerging Technologies in IT'],
        ];

        foreach ($subjects as [$year, $sem, $code, $desc]) {
            $is_universal = substr($code, 0, 2) === 'GE';
            CurriculumSubject::create([
                'curriculum_id' => $curriculum->id,
                'subject_code' => $code,
                'subject_description' => $desc,
                'year_level' => $year,
                'semester' => $sem,
                'is_universal' => $is_universal,
            ]);
        }
    }
}
