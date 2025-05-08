<?php

namespace App\Http\Controllers;

use App\Models\Activity;

use App\Models\AcademicPeriod;
use App\Models\Course;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================
    // Departments
    // ============================

    public function departments()
    {
        Gate::authorize('admin');

        $departments = Department::where('is_deleted', false)
            ->orderBy('department_code')
            ->get();

        return view('admin.departments', compact('departments'));
    }

    public function createDepartment()
    {
        Gate::authorize('admin');
        return view('admin.create-department');
    }

    public function storeDepartment(Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'department_code' => 'required|string|max:50',
            'department_description' => 'required|string|max:255',
        ]);

        Department::create([
            'department_code' => $request->department_code,
            'department_description' => $request->department_description,
            'is_deleted' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.departments')->with('success', 'Department added successfully.');
    }

    // ============================
    // Courses
    // ============================

    public static function dashboard()
    {
        $user = auth()->user();
        if ($user->role !== 3) {
            abort(403, 'Unauthorized');
        }

        $departments = Department::where('is_deleted', false)->get();
        $courses = Course::where('is_deleted', false)->get();
        $subjects = Subject::where('is_deleted', false)->get();
        // Get the most recent academic period
        $activePeriod = AcademicPeriod::where('is_deleted', false)
            ->orderBy('academic_year', 'desc')
            ->orderByRaw("FIELD(semester, '1st', '2nd', 'Summer')")
            ->first();

        // Get recent activities (last 30 days)
        $recentActivities = Activity::where('created_at', '>=', now()->subDays(30))
            ->whereIn('type', ['department', 'course', 'subject', 'academic_period'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'departments',
            'courses',
            'subjects',
            'activePeriod',
            'recentActivities'
        ));
    }

    public function courses()
    {
        Gate::authorize('admin');

        $courses = Course::where('is_deleted', false)
            ->orderBy('course_code')
            ->get();

        $departments = Department::where('is_deleted', false)
            ->orderBy('department_code')
            ->get();

        return view('admin.courses', compact('courses', 'departments'));
    }

    public function createCourse()
    {
        Gate::authorize('admin');

        $departments = Department::where('is_deleted', false)
            ->orderBy('department_code')
            ->get();

        return view('admin.create-course', compact('departments'));
    }

    public function storeCourse(Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'course_code' => 'required|string|max:50',
            'course_description' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        Course::create([
            'course_code' => $request->course_code,
            'course_description' => $request->course_description,
            'department_id' => $request->department_id,
            'is_deleted' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.courses')->with('success', 'Course added successfully.');
    }

    // ============================
    // Subjects
    // ============================

    public function subjects()
    {
        Gate::authorize('admin');

        $subjects = Subject::where('is_deleted', false)
            ->orderBy('subject_code')
            ->get();

        return view('admin.subjects', compact('subjects'));
    }

    public function createSubject()
    {
        Gate::authorize('admin');

        $departments = Department::where('is_deleted', false)->orderBy('department_code')->get();
        $courses = Course::where('is_deleted', false)->orderBy('course_code')->get();
        $academicPeriods = AcademicPeriod::orderBy('academic_year', 'desc')->orderBy('semester')->get();

        return view('admin.create-subject', compact('departments', 'courses', 'academicPeriods'));
    }

    public function storeSubject(Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'subject_code' => 'required|string|max:255|unique:subjects,subject_code',
            'subject_description' => 'required|string|max:255',
            'units' => 'required|integer|min:1|max:6',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        Subject::create([
            'subject_code' => $request->subject_code,
            'subject_description' => $request->subject_description,
            'units' => $request->units,
            'academic_period_id' => $request->academic_period_id,
            'department_id' => $request->department_id,
            'course_id' => $request->course_id,
            'is_deleted' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.subjects')->with('success', 'Subject added successfully.');
    }

    // ============================
    // Academic Periods (legacy fallback view)
    // ============================

    public function academicPeriods()
    {
        Gate::authorize('admin');

        $periods = AcademicPeriod::orderBy('academic_year', 'desc')->orderBy('semester')->get();
        return view('admin.academic-periods', compact('periods'));
    }
}
