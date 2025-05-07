<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Course;
use App\Models\Department;
use App\Models\AcademicPeriod;
use App\Models\UnverifiedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class GECoordinatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('ge_coordinator')) {
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        });
    }

    /**
     * Display the GE Coordinator dashboard.
     */
    public function dashboard()
    {
        return redirect()->route('dashboard');
    }


    /**
     * Display a listing of GE instructors.
     */
    public function manageInstructors()
    {
        // Get all active GE instructors (marked as universal)
        $instructors = User::where('role', 0)
            ->where('is_universal', true)
            ->orderBy('last_name')
            ->get();

        // Get pending GE instructors
        $pendingInstructors = UnverifiedUser::with('department', 'course')
            ->where('is_universal', true)
            ->get();

        $departments = Department::all();
        $courses = Course::all();

        return view('ge-coordinator.manage-instructors', [
            'instructors' => $instructors,
            'pendingInstructors' => $pendingInstructors,
            'departments' => $departments,
            'courses' => $courses
        ]);
    }


    /**
     * Display grades for GE students.
     */
    public function viewGrades(Request $request)
    {
        $query = Student::whereHas('subjects', function($query) {
            $query->where('is_universal', true);
        })
        ->with(['subjects' => function($query) {
            $query->where('is_universal', true);
        }, 'course']);

        // Apply filters
        if ($request->has('year_level') && $request->year_level) {
            $query->where('year_level', $request->year_level);
        }

        if ($request->has('academic_period_id') && $request->academic_period_id) {
            $query->where('academic_period_id', $request->academic_period_id);
        }

        $students = $query->orderBy('last_name')->paginate(20);
        $academicPeriods = AcademicPeriod::where('is_deleted', false)
            ->orderByDesc('academic_year')
            ->orderByRaw("FIELD(semester, '1st', '2nd', 'Summer')")
            ->get();

        return view('ge-coordinator.view-grades', compact(
            'students',
            'academicPeriods'
        ));
    }

    /**
     * Approve a pending instructor.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveInstructor($id)
    {
        $pendingInstructor = UnverifiedUser::findOrFail($id);
        
        // Check if this is a GE instructor
        if (!$pendingInstructor->is_universal) {
            return redirect()->back()->with('error', 'You can only approve GE instructors.');
        }

        DB::beginTransaction();
        try {
            // Create the instructor
            $instructor = User::create([
                'first_name' => $pendingInstructor->first_name,
                'middle_name' => $pendingInstructor->middle_name,
                'last_name' => $pendingInstructor->last_name,
                'email' => $pendingInstructor->email,
                'password' => $pendingInstructor->password,
                'department_id' => $pendingInstructor->department_id,
                'course_id' => $pendingInstructor->course_id,
                'is_universal' => true, // Force set to true for GE instructors
                'role' => 0, // Instructor role
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Delete the pending instructor record
            $pendingInstructor->delete();

            DB::commit();
            return redirect()->back()->with('success', 'GE Instructor approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve instructor: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pending instructor.
     */
    public function rejectInstructor($id)
    {
        $pendingInstructor = UnverifiedUser::findOrFail($id);
        
        // Check if this is a GE instructor
        if (!$pendingInstructor->is_universal) {
            return redirect()->back()->with('error', 'You can only reject GE instructors.');
        }
        
        // Delete the pending instructor record
        $pendingInstructor->delete();

        return redirect()->back()->with('success', 'GE Instructor rejected successfully.');
    }

    /**
     * Deactivate an instructor.
     */
    public function deactivateInstructor($id)
    {
        $instructor = User::where('id', $id)
            ->where('role', 0) // Instructor role
            ->firstOrFail();

        $instructor->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Instructor deactivated successfully.');
    }

    /**
     * Activate an instructor.
     */
    public function activateInstructor($id)
    {
        $instructor = User::where('id', $id)
            ->where('role', 0) // Instructor role
            ->firstOrFail();

        $instructor->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Instructor activated successfully.');
    }

    /**
     * Store a newly created instructor in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Create unverified user first
        $unverifiedUser = UnverifiedUser::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'course_id' => $request->course_id,
            'role' => 0, // Instructor role
        ]);

        return redirect()->route('ge-coordinator.instructors')
            ->with('success', 'Instructor account created successfully and pending approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not used in this implementation
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not used in this implementation
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Not used in this implementation
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not used in this implementation
        abort(404);
    }

    /**
     * Display the form for assigning GE subjects to instructors.
     */
    public function assignSubjects()
    {
        $academicPeriodId = session('active_academic_period_id');

        // Get all GE subjects for the current academic period
        $subjects = Subject::where('is_universal', true)
            ->where('is_deleted', false)
            ->where('academic_period_id', $academicPeriodId)
            ->orderBy('subject_code')
            ->get();

        // Get all active GE instructors
        $instructors = User::where('role', 0)
            ->where('is_universal', true)
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get();

        return view('ge-coordinator.assign-subjects', compact('subjects', 'instructors'));
    }

    /**
     * Store the assigned GE subject to an instructor.
     */
    public function storeAssignedSubject(Request $request)
    {
        $academicPeriodId = session('active_academic_period_id');

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'instructor_id' => 'required|exists:users,id',
        ]);

        // Verify the subject is a GE subject
        $subject = Subject::where('id', $request->subject_id)
            ->where('is_universal', true)
            ->where('academic_period_id', $academicPeriodId)
            ->firstOrFail();

        // Verify the instructor is a GE instructor
        $instructor = User::where('id', $request->instructor_id)
            ->where('role', 0)
            ->where('is_universal', true)
            ->where('is_active', true)
            ->firstOrFail();

        // Update the subject with the assigned instructor
        $subject->update([
            'instructor_id' => $instructor->id,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('ge-coordinator.assign.subjects')
            ->with('success', 'GE subject assigned successfully.');
    }
}
