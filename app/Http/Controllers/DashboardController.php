<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\{Student, Subject, TermGrade, FinalGrade, User, Course, UnverifiedUser};

class DashboardController extends Controller
{
    public function index()
    {
        if (Gate::allows('instructor')) {
            if (!session()->has('active_academic_period_id')) {
                return redirect()->route('select.academicPeriod');
            }

            $academicPeriodId = session('active_academic_period_id');
            $instructorId = Auth::id();

            $subjects = Subject::where('instructor_id', $instructorId)
                ->where('academic_period_id', $academicPeriodId)
                ->with('students')
                ->get();

            $instructorStudents = $subjects->flatMap->students
                ->where('is_deleted', false)
                ->unique('id')
                ->count();

            $enrolledSubjectsCount = $subjects->count();

            $subjectIds = $subjects->pluck('id');
            $finalGrades = FinalGrade::whereIn('subject_id', $subjectIds)
                ->where('academic_period_id', $academicPeriodId)
                ->get();

            $totalPassedStudents = $finalGrades->where('remarks', 'Passed')->count();
            $totalFailedStudents = $finalGrades->where('remarks', 'Failed')->count();

            $terms = ['prelim', 'midterm', 'prefinal', 'final'];
            $termCompletions = [];

            foreach ($terms as $term) {
                $termId = $this->getTermId($term);
                $total = 0;
                $graded = 0;

                foreach ($subjects as $subject) {
                    $studentCount = $subject->students->where('is_deleted', false)->count();
                    $gradedCount = TermGrade::where('subject_id', $subject->id)
                        ->where('term_id', $termId)
                        ->distinct('student_id')
                        ->count('student_id');

                    $total += $studentCount;
                    $graded += $gradedCount;
                }

                $termCompletions[$term] = [
                    'graded' => $graded,
                    'total' => $total,
                ];
            }

            $subjectCharts = [];
            foreach ($subjects as $subject) {
                $termsData = [];
                $termPercentages = [];

                foreach ($terms as $term) {
                    $termId = $this->getTermId($term);
                    $studentCount = $subject->students->where('is_deleted', false)->count();
                    $gradedCount = TermGrade::where('subject_id', $subject->id)
                        ->where('term_id', $termId)
                        ->distinct('student_id')
                        ->count('student_id');

                    $percentage = $studentCount > 0 ? round(($gradedCount / $studentCount) * 100, 2) : 0;

                    $termsData[$term] = [
                        'graded' => $gradedCount,
                        'total' => $studentCount,
                        'percentage' => $percentage,
                    ];

                    $termPercentages[] = $percentage;
                }

                $subjectCharts[] = [
                    'code' => $subject->subject_code,
                    'description' => $subject->subject_description,
                    'terms' => $termsData,
                    'termPercentages' => $termPercentages,
                ];
            }

            return view('dashboard.instructor', compact(
                'instructorStudents',
                'enrolledSubjectsCount',
                'totalPassedStudents',
                'totalFailedStudents',
                'termCompletions',
                'subjectCharts'
            ));
        }

        if (Gate::allows('chairperson')) {
            if (!session()->has('active_academic_period_id')) {
                return redirect()->route('select.academicPeriod');
            }
            
            $departmentId = auth()->user()->department_id;
            
            // Number of instructors (Full-time / Part-time)
            $instructors = User::where('role', 0)
                ->where('department_id', $departmentId)
                ->where('is_universal', false)
                ->get();
                
            $fullTimeCount = $instructors->where('is_fulltime', true)->count();
            $partTimeCount = $instructors->where('is_fulltime', false)->count();
            
            // Total number of courses in the department
            $totalCourses = Course::where('department_id', $departmentId)->count();
            
            // Total number of students per course
            $coursesWithStudents = Course::withCount('students')
                ->where('department_id', $departmentId)
                ->get();
                
            // Number of students per year level
            $yearLevels = Student::selectRaw('year_level, COUNT(*) as count')
                ->groupBy('year_level')
                ->orderBy('year_level')
                ->pluck('count', 'year_level');
            
            return view('dashboard.chairperson', [
                'fullTimeCount' => $fullTimeCount,
                'partTimeCount' => $partTimeCount,
                'totalCourses' => $totalCourses,
                'coursesWithStudents' => $coursesWithStudents,
                'yearLevels' => $yearLevels
            ]);
        }

        if (Gate::allows('admin')) {
            return view('dashboard.admin');
        }

        if (Gate::allows('dean')) {
            if (!session()->has('active_academic_period_id')) {
                return redirect()->route('select.academicPeriod');
            }

            // Get total students count
            $studentCount = Student::where('is_deleted', 0)->count();
            // Get total instructors count
            $instructorCount = User::where('role', 0)->count();
            // Get total courses count
            $courseCount = Course::count();
            // Get total departments count
            $departmentCount = \App\Models\Department::count();

            // Get recent activities (sample data - replace with actual data)
            $recentActivities = [
                [
                    'type' => 'user',
                    'icon' => 'fa-user-plus',
                    'description' => 'New student registered',
                    'time' => '2 minutes ago'
                ],
                [
                    'type' => 'course',
                    'icon' => 'fa-book',
                    'description' => 'New course added',
                    'time' => '1 hour ago'
                ],
                [
                    'type' => 'grade',
                    'icon' => 'fa-star',
                    'description' => 'Grades updated for CS101',
                    'time' => '3 hours ago'
                ],
                [
                    'type' => 'announcement',
                    'icon' => 'fa-bullhorn',
                    'description' => 'New announcement posted',
                    'time' => '1 day ago'
                ],
            ];

            return view('dashboard.dean', [
                'studentCount' => $studentCount,
                'instructorCount' => $instructorCount,
                'courseCount' => $courseCount,
                'departmentCount' => $departmentCount,
                'recentActivities' => $recentActivities
            ]);
        }

        if (Gate::allows('ge_coordinator')) {
            if (!session()->has('active_academic_period_id')) {
                return redirect()->route('select.academicPeriod');
            }
            
            $instructorCount = User::where('role', 0) // Instructor role
                ->whereHas('subjects', function($query) {
                    $query->where('is_universal', true);
                })
                ->count();

            $studentCount = Student::whereHas('subjects', function($query) {
                    $query->where('is_universal', true);
                })
                ->count();

            $subjectCount = Subject::where('is_universal', true)->count();


            $pendingInstructors = UnverifiedUser::with('department', 'course')
                ->where('is_universal', true)
                ->get();

            $recentStudents = Student::whereHas('subjects', function($query) {
                    $query->where('is_universal', true);
                })
                ->with('course')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return view('dashboard.ge-coordinator', [
                'instructorCount' => $instructorCount,
                'studentCount' => $studentCount,
                'subjectCount' => $subjectCount,
                'recentStudents' => $recentStudents,
                'pendingInstructors' => $pendingInstructors
            ]);
        }

        abort(403, 'Unauthorized access.');
    }

    private function getTermId($term)
    {
        return [
            'prelim' => 1,
            'midterm' => 2,
            'prefinal' => 3,
            'final' => 4,
        ][$term] ?? null;
    }
}
