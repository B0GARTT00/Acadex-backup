<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChairpersonController;
use App\Http\Controllers\Chairperson\AccountApprovalController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\DeanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GECoordinatorController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\FinalGradeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\StudentImportController;
use App\Http\Middleware\EnsureAcademicPeriodSet;

// Welcome Page
Route::get('/', fn () => view('welcome'));

// Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Academic Period Selection
Route::middleware(['auth'])->group(function () {
    Route::get('/select-academic-period', function () {
        $periods = \App\Models\AcademicPeriod::where('is_deleted', false)
            ->orderByDesc('academic_year')
            ->orderByRaw("FIELD(semester, '1st', '2nd', 'Summer')")
            ->get();

        return view('instructor.select-academic-period', compact('periods'));
    })->name('select.academicPeriod');

    Route::post('/set-academic-period', function (Request $request) {
        $request->validate([
            'academic_period_id' => 'required|exists:academic_periods,id',
        ]);
        session(['active_academic_period_id' => $request->academic_period_id]);
        
        // Redirect to appropriate dashboard based on role
        $user = auth()->user();
        switch ($user->role) {
            case 0: // Instructor
                return redirect()->route('instructor.dashboard');
            case 2: // Chairperson
                return redirect()->route('chairperson.dashboard');
            case 4: // GE Coordinator
                return redirect()->route('ge-coordinator.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    })->name('set.academicPeriod');
});

// Dashboard
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 0) { // Instructor
        return redirect()->route('instructor.dashboard');
    } elseif ($user->role === 1) { // Chairperson
        return redirect()->route('chairperson.dashboard');
    } elseif ($user->role === 2 || $user->role === 3) { // Dean or Admin
        $departments = \App\Models\Department::where('is_deleted', false)->get();
        $courses = \App\Models\Course::where('is_deleted', false)->get();
        $subjects = \App\Models\Subject::where('is_deleted', false)->get();
        $academicPeriod = \App\Models\AcademicPeriod::where('is_deleted', false)
            ->orderByDesc('academic_year')
            ->orderByRaw("FIELD(semester, '1st', '2nd', 'Summer')")
            ->first();
        $activities = \App\Models\Activity::where('created_at', '>=', now()->subDays(7))
            ->whereIn('type', ['department', 'course', 'subject', 'academic_period'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Use the latest academic period as the active one
        $activePeriod = $academicPeriod;

        return view('dashboard.admin', compact('departments', 'courses', 'subjects', 'academicPeriod', 'activities', 'activePeriod'));
    } elseif ($user->role === 4) { // GE Coordinator
        // Check if academic period is set in session
        $activePeriodId = session('active_academic_period_id');
        if (!$activePeriodId) {
            return redirect()->route('select-academic-period');
        }

        return redirect()->route('ge-coordinator.dashboard');
    }
    return view('dashboard.admin');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Chairperson Routes
Route::prefix('chairperson')
    ->middleware(['auth'])
    ->name('chairperson.')
    ->group(function () {
        Route::get('/instructors', [ChairpersonController::class, 'manageInstructors'])->name('instructors');
        Route::get('/instructors/create', [ChairpersonController::class, 'createInstructor'])->name('createInstructor');
        Route::post('/instructors/store', [ChairpersonController::class, 'storeInstructor'])->name('storeInstructor');
        Route::post('/instructors/{id}/deactivate', [ChairpersonController::class, 'deactivateInstructor'])->name('deactivateInstructor');
        Route::post('/instructors/{id}/request-ge', [\App\Http\Controllers\GESubjectRequestController::class, 'store'])->name('requestGE');

        Route::get('/assign-subjects', [ChairpersonController::class, 'assignSubjects'])->name('assignSubjects');
        Route::post('/assign-subjects/store', [ChairpersonController::class, 'storeAssignedSubject'])->name('storeAssignedSubject');

        Route::get('/grades', [ChairpersonController::class, 'viewGrades'])->name('viewGrades');
        Route::get('/students-by-year', [ChairpersonController::class, 'viewStudentsPerYear'])->name('studentsByYear');

        Route::get('/approvals', [AccountApprovalController::class, 'index'])->name('accounts.index');
        Route::post('/approvals/{id}/approve', [AccountApprovalController::class, 'approve'])->name('accounts.approve');
        Route::post('/approvals/{id}/reject', [AccountApprovalController::class, 'reject'])->name('accounts.reject');

        Route::get('/ge-requests', [\App\Http\Controllers\GESubjectRequestController::class, 'index'])->name('ge-requests');
        Route::post('/ge-requests/{id}/approve', [\App\Http\Controllers\GESubjectRequestController::class, 'approve'])->name('ge-requests.approve');
        Route::post('/ge-requests/{id}/reject', [\App\Http\Controllers\GESubjectRequestController::class, 'reject'])->name('ge-requests.reject');
    });

// Chairperson Dashboard
Route::get('/chairperson/dashboard', [ChairpersonController::class, 'dashboard'])->name('chairperson.dashboard');

// Curriculum Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/curriculum/select-subjects', [CurriculumController::class, 'selectSubjectsChair'])->name('curriculum.selectSubjects');
    Route::post('/curriculum/confirm-subjects', [CurriculumController::class, 'confirmSubjects'])->name('curriculum.confirmSubjects');
    Route::get('/curriculum/{curriculum}/fetch', [CurriculumController::class, 'fetchSubjects'])
        ->name('curriculum.fetchSubjects');
});

// Admin Routes
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::get('/departments/create', [AdminController::class, 'createDepartment'])->name('createDepartment');
    Route::post('/departments/store', [AdminController::class, 'storeDepartment'])->name('storeDepartment');

    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('createCourse');
    Route::post('/courses/store', [AdminController::class, 'storeCourse'])->name('storeCourse');

    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
    Route::get('/subjects/create', [AdminController::class, 'createSubject'])->name('createSubject');
    Route::post('/subjects/store', [AdminController::class, 'storeSubject'])->name('storeSubject');

    Route::get('/academic-periods', [AcademicPeriodController::class, 'index'])->name('admin.academicPeriods.index');
    Route::post('/academic-periods/generate', [AcademicPeriodController::class, 'generate'])->name('admin.academicPeriods.generate');
    Route::get('/academic-periods/create', [AcademicPeriodController::class, 'create'])->name('admin.academicPeriods.create');
    Route::post('/academic-periods', [AcademicPeriodController::class, 'store'])->name('admin.academicPeriods.store');
});

// GE Coordinator Routes
Route::prefix('ge-coordinator')
    ->middleware(['auth', 'academic.period.set'])
    ->name('ge-coordinator.')
    ->group(function () {
        // GE Subject Requests
        Route::get('/ge-requests', [GECoordinatorController::class, 'showGERequests'])->name('showGERequests');
        Route::post('/requests/approve/{id}', [GECoordinatorController::class, 'approveGERequest'])->name('approveGERequest');
        Route::post('/requests/reject/{id}', [GECoordinatorController::class, 'rejectGERequest'])->name('rejectGERequest');
        
        Route::get('/dashboard', [GECoordinatorController::class, 'dashboard'])->name('dashboard');
        Route::get('/instructors', [GECoordinatorController::class, 'manageInstructors'])->name('manageInstructors');
        Route::get('/grades', [GECoordinatorController::class, 'viewGrades'])->name('viewGrades');
        Route::post('/instructors/{id}/approve', [GECoordinatorController::class, 'approveInstructor'])->name('approveInstructor');
        
        // GE Subjects Assignment
        Route::get('/assign-subjects', [GECoordinatorController::class, 'assignSubjects'])->name('assign.subjects');
        Route::post('/assign-subjects', [GECoordinatorController::class, 'storeAssignedSubject'])->name('store.assigned.subject');
        
        // GE Subjects Management
        Route::get('/subjects', [GECoordinatorController::class, 'subjects'])->name('subjects.index');
        Route::get('/subjects/import', [GECoordinatorController::class, 'importSubjects'])->name('subjects.import');
        Route::get('/subjects/{curriculum}/fetch', [GECoordinatorController::class, 'fetchSubjects'])->name('subjects.fetch');
        Route::post('/subjects/confirm', [GECoordinatorController::class, 'confirmSubjects'])->name('subjects.confirm');
        Route::get('/subjects/create', [GECoordinatorController::class, 'createSubject'])->name('subjects.create');
        Route::post('/subjects', [GECoordinatorController::class, 'storeSubject'])->name('subjects.store');
        Route::get('/subjects/{subject}/edit', [GECoordinatorController::class, 'editSubject'])->name('subjects.edit');
        Route::put('/subjects/{subject}', [GECoordinatorController::class, 'updateSubject'])->name('subjects.update');
        Route::match(['post', 'delete'], '/instructors/{id}/reject', [GECoordinatorController::class, 'rejectInstructor'])->name('instructors.reject');
        Route::patch('/instructors/deactivate/{id}', [GECoordinatorController::class, 'deactivateInstructor'])->name('instructors.deactivate');
        Route::patch('/instructors/activate/{id}', [GECoordinatorController::class, 'activateInstructor'])->name('instructors.activate');
    });

// Instructor Routes
Route::prefix('instructor')
    ->middleware(['auth', EnsureAcademicPeriodSet::class])
    ->name('instructor.')
    ->group(function () {
        Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');

        // Student Management
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/enroll', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::delete('/students/{student}/drop', [StudentController::class, 'drop'])->name('students.drop');

        // ✅ Student Import Routes
        Route::get('/students/import', [StudentImportController::class, 'showUploadForm'])->name('students.import');
        Route::post('/students/import', [StudentImportController::class, 'upload'])->name('students.import.upload');
        Route::post('/students/import/confirm', [StudentImportController::class, 'confirmImport'])->name('students.import.confirm');

        // Grades
        Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
        Route::get('/grades/partial', [GradeController::class, 'partial'])->name('grades.partial');
        Route::post('/grades/save', [GradeController::class, 'store'])->name('grades.store');
        Route::post('/grades/ajax-save-score', [GradeController::class, 'ajaxSaveScore'])->name('grades.ajaxSaveScore');

        // Final Grades
        Route::get('/final-grades', [FinalGradeController::class, 'index'])->name('final-grades.index');
        Route::post('/final-grades/generate', [FinalGradeController::class, 'generate'])->name('final-grades.generate');

        // Activities
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
        Route::post('/activities/store', [ActivityController::class, 'store'])->name('activities.store');
        Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activities/{id}', [ActivityController::class, 'delete'])->name('activities.delete');
    });

// Dean Routes
Route::prefix('dean')->middleware('auth')->name('dean.')->group(function () {
    Route::get('/instructors', [DeanController::class, 'viewInstructors'])->name('instructors');
    Route::get('/students', [DeanController::class, 'viewStudents'])->name('students');
    Route::get('/grades', [DeanController::class, 'viewGrades'])->name('grades');
    Route::get('/instructor/grades/partial', [GradeController::class, 'partial'])->name('instructor.grades.partial');
    Route::get('/dean/students', [DeanController::class, 'viewStudents'])->name('dean.students');
});

// Admin Routes
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::get('/departments/create', [AdminController::class, 'createDepartment'])->name('createDepartment');
    Route::post('/departments/store', [AdminController::class, 'storeDepartment'])->name('storeDepartment');

    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('createCourse');
    Route::post('/courses/store', [AdminController::class, 'storeCourse'])->name('storeCourse');

    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
    Route::get('/subjects/create', [AdminController::class, 'createSubject'])->name('createSubject');
    Route::post('/subjects/store', [AdminController::class, 'storeSubject'])->name('storeSubject');

    Route::get('/academic-periods', [AcademicPeriodController::class, 'index'])->name('academicPeriods');
    Route::post('/academic-periods/generate', [AcademicPeriodController::class, 'generate'])->name('academicPeriods.generate');
});

// GE Coordinator Routes
Route::prefix('ge-coordinator')
    ->middleware(['auth', 'academic.period.set'])
    ->name('ge-coordinator.')
    ->group(function () {
        Route::get('/dashboard', [GECoordinatorController::class, 'dashboard'])->name('dashboard');
        Route::get('/ge-requests', [GECoordinatorController::class, 'manageGERequests'])->name('ge-requests');
        Route::post('/ge-requests/{id}/approve', [GECoordinatorController::class, 'approveGERequest'])->name('ge-requests.approve');
        Route::post('/ge-requests/{id}/reject', [GECoordinatorController::class, 'rejectGERequest'])->name('ge-requests.reject');
        Route::get('/instructors', [GECoordinatorController::class, 'manageInstructors'])->name('instructors');
        Route::get('/grades', [GECoordinatorController::class, 'viewGrades'])->name('grades');
        Route::post('/instructors/{id}/approve', [GECoordinatorController::class, 'approveInstructor'])->name('instructors.approve');
        
        // GE Subjects Assignment
        Route::get('/assign-subjects', [GECoordinatorController::class, 'assignSubjects'])->name('assign.subjects');
        Route::post('/assign-subjects', [GECoordinatorController::class, 'storeAssignedSubject'])->name('store.assigned.subject');
        
        // GE Subjects Management
        Route::get('/subjects', [GECoordinatorController::class, 'subjects'])->name('subjects.index');
        Route::get('/subjects/import', [GECoordinatorController::class, 'importSubjects'])->name('subjects.import');
        Route::get('/subjects/{curriculum}/fetch', [GECoordinatorController::class, 'fetchSubjects'])->name('subjects.fetch');
        Route::post('/subjects/confirm', [GECoordinatorController::class, 'confirmSubjects'])->name('subjects.confirm');
        Route::get('/subjects/create', [GECoordinatorController::class, 'createSubject'])->name('subjects.create');
        Route::post('/subjects', [GECoordinatorController::class, 'storeSubject'])->name('subjects.store');
        Route::get('/subjects/{subject}/edit', [GECoordinatorController::class, 'editSubject'])->name('subjects.edit');
        Route::put('/subjects/{subject}', [GECoordinatorController::class, 'updateSubject'])->name('subjects.update');
        Route::match(['post', 'delete'], '/instructors/{id}/reject', [GECoordinatorController::class, 'rejectInstructor'])->name('instructors.reject');
        Route::patch('/instructors/deactivate/{id}', [GECoordinatorController::class, 'deactivateInstructor'])->name('instructors.deactivate');
        Route::patch('/instructors/activate/{id}', [GECoordinatorController::class, 'activateInstructor'])->name('instructors.activate');
    });

// Auth Routes (Breeze/Fortify)
require __DIR__.'/auth.php';
