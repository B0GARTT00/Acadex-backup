<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Course;
use App\Models\Department;
use App\Models\AcademicPeriod;
use App\Models\UnverifiedUser;
use App\Models\GESubjectRequest;
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
        $academicPeriodId = session('active_academic_period_id');

        // Get instructor counts
        $instructors = User::where('role', 0) // Instructor role
            ->where('is_universal', true)
            ->get();

        $instructorCount = $instructors->count();
        $studentCount = Student::where('is_deleted', false)
            ->whereHas('course', function ($query) {
                $query->where('department_id', 1);
            })
            ->count();

        // Get GE subjects
        $geSubjects = Subject::where('is_universal', true)
            ->where('is_deleted', false)
            ->where('academic_period_id', $academicPeriodId)
            ->with('department')
            ->get();

        $subjectCount = $geSubjects->count();

        $pendingInstructors = User::where('role', 0) // Instructor role
            ->where('is_universal', true)
            ->where('is_active', false)
            ->get();



        // Get pending GE requests
        $pendingRequests = GESubjectRequest::where('status', 'pending')
            ->with(['instructor', 'department', 'chairperson'])
            ->get();

        return view('dashboard.ge-coordinator', compact(
            'instructorCount',
            'studentCount',
            'subjectCount',
            'pendingInstructors',
            'pendingRequests',
            'geSubjects'
        ));
    }

    /**
     * Display a listing of GE instructors.
     */
    public function manageInstructors()
    {
        // Get all GE instructors (marked as universal)
        $instructors = User::where('role', 0)
            ->where('is_universal', true)
            ->where('course_id', 4) // Course ID for General Education
            ->orderBy('last_name')
            ->get();

        // Get pending GE instructors
        $pendingInstructors = UnverifiedUser::with('department', 'course')
            ->where('course_id', 4) // Course ID for General Education
            ->get();

        $departments = Department::all();
        $courses = Course::all();

        return view('ge-coordinator.manage-instructors', compact(
            'instructors',
            'pendingInstructors',
            'departments',
            'courses'
        ));
    }

    /**
     * Display GE subject requests.
     */
    public function manageGERequests()
    {
        $requests = GESubjectRequest::where('status', 'pending')
            ->whereHas('chairperson', function($query) {
                $query->where('role', 2); // Chairperson role
            })
            ->with(['instructor', 'department', 'chairperson'])
            ->get();

        $pendingInstructors = UnverifiedUser::with('department')
            ->whereHas('department', function($query) {
                $query->where('department_code', 'GE'); // Filter by GE department
            })
            ->get();

        return view('ge-coordinator.ge-requests', compact('requests', 'pendingInstructors'));
    }

    /**
     * Display GE subjects.
     */
    public function subjects()
    {
        $academicPeriodId = session('active_academic_period_id');
        
        // Debug information
        \Log::info('Active Academic Period ID: ' . $academicPeriodId);
        
        $subjects = Subject::where('is_universal', true)
            ->where('is_deleted', false)
            ->where('academic_period_id', $academicPeriodId)
            ->with('department')
            ->get();

        // Debug subjects count
        \Log::info('Number of subjects found: ' . $subjects->count());

        return view('ge-coordinator.subjects', compact('subjects'));
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

        DB::beginTransaction();
        try {
            // Create new instructor record in User table
            $instructor = User::create([
                'first_name' => $pendingInstructor->first_name,
                'middle_name' => $pendingInstructor->middle_name,
                'last_name' => $pendingInstructor->last_name,
                'email' => $pendingInstructor->email,
                'password' => bcrypt($pendingInstructor->password),
                'department_id' => $pendingInstructor->department_id,
                'course_id' => 4, // Set to 4 for GE instructors
                'is_universal' => true, // Mark as GE instructor
                'role' => 0, // Instructor role
                'is_active' => true
            ]);

            // Set email verification
            $instructor->email_verified_at = now();
            $instructor->save();

            // Delete the pending instructor record
            $pendingInstructor->delete();

            DB::commit();
            return redirect()->route('ge-coordinator.manageInstructors')->with('success', 'GE instructor approved successfully.');
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
     * Approve a GE subject request.
     */
    public function approveGERequest($id)
    {
        $request = GESubjectRequest::findOrFail($id);

        // Check if the request is already approved
        if ($request->status === 'approved') {
            return redirect()->back()->with('error', 'This request has already been approved.');
        }

        // Update the request status
        $request->update([
            'status' => 'approved',
            'response_reason' => 'Request approved by GE Coordinator',
        ]);

        // Get the instructor and update their GE status
        $instructor = User::findOrFail($request->instructor_id);
        
        // Ensure the instructor is marked as GE instructor and activated
        $instructor->update([
            'is_universal' => true, // Mark as GE instructor
            'is_active' => true,    // Activate the instructor
            'role' => 0,           // Ensure role is set to instructor
        ]);

        return redirect()->back()->with('success', 'GE subject request approved successfully. Instructor has been added to GE instructor management.');
    }

    /**
     * Reject a GE subject request.
     */
    public function rejectGERequest($id)
    {
        $request = GESubjectRequest::findOrFail($id);

        // Check if the request is already rejected
        if ($request->status === 'rejected') {
            return redirect()->back()->with('error', 'This request has already been rejected.');
        }

        // Update the request status
        $request->update([
            'status' => 'rejected',
            'response_reason' => request('response_reason', 'Request rejected by GE Coordinator'),
        ]);

        return redirect()->back()->with('success', 'GE subject request rejected successfully.');
    }

    /**
     * Deactivate a GE instructor.
     */
    public function deactivateGEInstructor($id)
    {
        $instructor = User::where('id', $id)
            ->where('role', 0) // Instructor role
            ->where('is_universal', true) // GE instructor
            ->firstOrFail();

        $instructor->update(['is_active' => false]);

        return redirect()->back()->with('success', 'GE Instructor deactivated successfully.');
    }

    /**
     * Activate a GE instructor.
     */
    public function activateGEInstructor($id)
    {
        $instructor = User::where('id', $id)
            ->where('role', 0) // Instructor role
            ->where('is_universal', true) // GE instructor
            ->firstOrFail();

        $instructor->update(['is_active' => true]);

        return redirect()->back()->with('success', 'GE Instructor activated successfully.');
    }

    /**
     * Display the form for importing GE subjects from curriculum.
     */
    public function importSubjects()
    {
        $curriculums = \App\Models\Curriculum::with('course')->get();
        
        // Debug: Log the curriculums
        \Log::info('Curriculums:', ['curriculums' => $curriculums->toArray()]);
        
        return view('ge-coordinator.import-subjects', compact('curriculums'));
    }



    /**
     * Fetch subjects from a curriculum.
     */
    public function fetchSubjects($curriculumId)
    {
        $subjects = \App\Models\CurriculumSubject::where('curriculum_id', $curriculumId)
            ->with(['curriculum.course'])
            ->get()
            ->map(function ($curriculumSubject) {
                // Determine if this is a GE subject based on subject code
                $isGE = false;
                if (strtoupper(substr($curriculumSubject->subject_code, 0, 2)) === 'GE') {
                    $isGE = true;
                } elseif (in_array(strtoupper($curriculumSubject->subject_code), [
                    'NSTP 1', 'NSTP 2', 'PE 1', 'PE 2', 'PD 1', 'PD 2', 'RS 1', 'RS 2'
                ])) {
                    $isGE = true;
                }

                return [
                    'id' => $curriculumSubject->id,
                    'subject_code' => $curriculumSubject->subject_code,
                    'subject_description' => $curriculumSubject->subject_description,
                    'year_level' => $curriculumSubject->year_level,
                    'semester' => $curriculumSubject->semester,
                    'curriculum' => $curriculumSubject->curriculum->name,
                    'course' => $curriculumSubject->curriculum->course->course_code ?? 'N/A',
                    'is_universal' => $isGE
                ];
            });

        return response()->json($subjects);
    }

    /**
     * Confirm and import selected GE subjects.
     */
    public function confirmSubjects(Request $request)
    {
        $request->validate([
            'curriculum_id' => 'required|exists:curriculums,id',
            'subject_ids' => 'required|array',
        ]);

        $academicPeriodId = session('active_academic_period_id');

        DB::beginTransaction();
        try {
            $subjects = \App\Models\CurriculumSubject::where('curriculum_id', $request->curriculum_id)
                ->whereIn('id', $request->subject_ids)
                ->get();

            foreach ($subjects as $curriculumSubject) {
                \App\Models\Subject::firstOrCreate([
                    'subject_code' => $curriculumSubject->subject_code,
                    'academic_period_id' => $academicPeriodId
                ], [
                    'subject_description' => $curriculumSubject->subject_description,
                    'year_level' => $curriculumSubject->year_level,
                    'semester' => $curriculumSubject->semester,
                    'department_id' => null,
                    'course_id' => null,
                    'is_universal' => true,
                    'is_deleted' => false,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            DB::commit();
            return redirect()->route('ge-coordinator.assign-subjects')
                ->with('success', 'GE subjects imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to import subjects: ' . $e->getMessage());
        }
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
     * Display the form for assigning GE subjects to instructors.
     */
    public function assignSubjects()
    {
        $academicPeriodId = session('active_academic_period_id');

        // Get all GE subjects for the current academic period
        $subjects = \App\Models\Subject::where('is_universal', true)
            ->where('is_deleted', false)
            ->where('academic_period_id', $academicPeriodId)
            ->orderBy('subject_code')
            ->get();

        // Get all active GE instructors
        $instructors = \App\Models\User::where('role', 0)
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
        $subject = \App\Models\Subject::where('id', $request->subject_id)
            ->where('is_universal', true)
            ->where('academic_period_id', $academicPeriodId)
            ->firstOrFail();

        // Verify the instructor is a GE instructor
        $instructor = \App\Models\User::where('id', $request->instructor_id)
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
