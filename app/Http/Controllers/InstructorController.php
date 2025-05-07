<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Course;
use App\Models\TermGrade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Instructor dashboard
    public function dashboard()
    {
        Gate::authorize('instructor');
        $instructor = Auth::user();
        
        // Initialize default values
        $instructorStudents = 0;
        $enrolledSubjectsCount = 0;
        $totalPassedStudents = 0;
        $totalFailedStudents = 0;
        $termCompletions = [
            'prelim' => ['graded' => 0, 'total' => 0],
            'midterm' => ['graded' => 0, 'total' => 0],
            'prefinal' => ['graded' => 0, 'total' => 0],
            'final' => ['graded' => 0, 'total' => 0]
        ];
        $subjectCharts = [];
        
        // Get the instructor's subjects for the current academic period
        $academicPeriodId = session('active_academic_period_id');
        
        if ($academicPeriodId) {
            // Get the instructor's subjects with eager loading of students and grades
            $subjects = $instructor->subjects()
                ->where('academic_period_id', $academicPeriodId)
                ->where('is_deleted', false)
                ->with(['students', 'grades'])
                ->get();
                
            // Count unique students across all subjects
            $instructorStudents = $subjects->flatMap(function($subject) {
                return $subject->students;
            })->unique('id')->count();
            
            $enrolledSubjectsCount = $subjects->count();
            
            // Initialize term completions and prepare subject charts data
            foreach ($subjects as $subject) {
                $totalStudents = $subject->students->count();
                $subjectChart = [
                    'code' => $subject->subject_code,
                    'description' => $subject->description,
                    'terms' => [],
                    'termPercentages' => []
                ];
                
                foreach (['prelim', 'midterm', 'prefinal', 'final'] as $term) {
                    $termCompletions[$term]['total'] += $totalStudents;
                    
                    // Count how many students have grades for this term
                    $gradedCount = $subject->grades
                        ->where('term', $term)
                        ->count();
                        
                    $termCompletions[$term]['graded'] += $gradedCount;
                    
                    // Calculate percentage for this term
                    $percentage = $totalStudents > 0 ? round(($gradedCount / $totalStudents) * 100) : 0;
                    
                    // Add data for subject chart
                    $subjectChart['terms'][$term] = [
                        'graded' => $gradedCount,
                        'total' => $totalStudents,
                        'percentage' => $percentage
                    ];
                    
                    // Add to term percentages for chart
                    $subjectChart['termPercentages'][] = $percentage;
                }
                
                $subjectCharts[] = $subjectChart;
            }
        }

        return view('dashboard.instructor', compact(
            'instructor',
            'instructorStudents',
            'enrolledSubjectsCount',
            'totalPassedStudents',
            'totalFailedStudents',
            'termCompletions',
            'subjectCharts'
        ));
    }

    // Manage Students Page (with subject grade status labels)
    public function index(Request $request)
    {
        Gate::authorize('instructor');

        $academicPeriodId = session('active_academic_period_id');
        $term = $request->query('term', 'prelim');

        $subjects = collect();

        if ($academicPeriodId) {
            $subjects = Subject::where('instructor_id', Auth::id())
                ->where('is_deleted', false)
                ->where('academic_period_id', $academicPeriodId)
                ->withCount('students')
                ->get();

            foreach ($subjects as $subject) {
                $totalStudents = $subject->students_count;

                $graded = TermGrade::where('subject_id', $subject->id)
                ->where('term', $term)
                ->distinct('student_id')
                    ->count('student_id');

                $subject->grade_status = match (true) {
                    $graded === 0 => 'not_started',
                    $graded < $totalStudents => 'pending',
                    default => 'completed'
                };
            }
        }

        $courses = Course::all();
        $students = collect();

        if ($request->filled('subject_id')) {
            $subject = Subject::findOrFail($request->subject_id);

            if ($subject->instructor_id !== Auth::id()) {
                abort(403, 'Unauthorized access to subject.');
            }

            $students = $subject->students()
                ->where('students.is_deleted', 0)
                ->get();
        }

        return view('instructor.manage-students', compact('subjects', 'students', 'courses'));
    }

    // Shared helper for term mapping
    public static function getTermId($term)
    {
        return [
            'prelim' => 1,
            'midterm' => 2,
            'prefinal' => 3,
            'final' => 4,
        ][$term] ?? null;
    }
}
