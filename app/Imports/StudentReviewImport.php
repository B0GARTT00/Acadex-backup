<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\ReviewStudent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentReviewImport implements ToCollection
{
    protected $subjectId;
    protected $listName;

    public function __construct($subjectId, $listName)
    {
        $this->subjectId = $subjectId; // Can be null
        $this->listName = is_string($listName) ? $listName : 'Untitled List';
    }

    public function collection(Collection $rows)
    {
        // Skip the header row
        $rows->shift();

        foreach ($rows as $row) {
            // Required fields: last name, first name, year level, course code
            if (empty($row[0]) || empty($row[1]) || empty($row[3]) || empty($row[4])) {
                continue;
            }

            $courseCode = strtoupper(trim($row[4]));
            $course = Course::where('course_code', $courseCode)->first();

            if (!$course) {
                // Optionally log or skip if course doesn't exist
                continue;
            }

            ReviewStudent::create([
                'instructor_id' => Auth::id(),
                'list_name'     => $this->listName,
                'last_name'     => trim($row[0]),
                'first_name'    => trim($row[1]),
                'middle_name'   => $row[2] ?? null,
                'year_level'    => (int) $row[3],
                'course_id'     => $course->id,
                'subject_id'    => $this->subjectId,
            ]);
        }
    }
}
