<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CurriculumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Gate::authorize('admin-chair');

        $curriculums = Curriculum::with('course')
            ->orderByDesc('created_at')
            ->get();

        return view('curriculum.index', compact('curriculums'));
    }

    public function show(Curriculum $curriculum)
    {
        Gate::authorize('admin-chair');

        $subjects = $curriculum->subjects()->orderBy('year_level')->orderBy('semester')->get();

        return view('curriculum.show', compact('curriculum', 'subjects'));
    }

    public function create()
    {
        Gate::authorize('admin-chair');

        $courses = Course::where('is_deleted', false)->orderBy('course_code')->get();

        return view('curriculum.create', compact('courses'));
    }

    public function store(Request $request)
    {
        Gate::authorize('admin-chair');

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255|unique:curriculums,name',
        ]);

        Curriculum::create([
            'course_id' => $request->course_id,
            'name' => $request->name,
            'is_active' => true,
        ]);

        return redirect()->route('curriculum.index')->with('success', 'Curriculum created successfully.');
    }

    public function destroy(Curriculum $curriculum)
    {
        Gate::authorize('admin-chair');

        $curriculum->delete();

        return redirect()->route('curriculum.index')->with('success', 'Curriculum deleted.');
    }

    public function addSubject(Request $request, Curriculum $curriculum)
    {
        Gate::authorize('admin-chair');

        $request->validate([
            'subject_code' => 'required|string|max:255',
            'subject_description' => 'required|string|max:255',
            'year_level' => 'required|integer',
            'semester' => 'required|string',
        ]);

        $curriculum->subjects()->create([
            'subject_code' => $request->subject_code,
            'subject_description' => $request->subject_description,
            'year_level' => $request->year_level,
            'semester' => $request->semester,
        ]);

        return redirect()->route('curriculum.show', $curriculum)->with('success', 'Subject added to curriculum.');
    }

    public function removeSubject(CurriculumSubject $subject)
    {
        Gate::authorize('admin-chair');

        $subject->delete();

        return redirect()->back()->with('success', 'Subject removed from curriculum.');
    }

    public function selectSubjects()
    {
        Gate::authorize('admin-chair');
        
        // Get all curriculums with their associated courses
        $curriculums = Curriculum::with('course')
            ->orderByDesc('created_at')
            ->get();

        return view('chairperson.select-curriculum-subjects', compact('curriculums'));
    }

    public function confirmSubjects(Request $request)
    {
        Gate::authorize('admin-chair');

        $request->validate([
            'curriculum_id' => 'required|exists:curriculums,id',
            'subjects' => 'required|array',
        ]);

        $curriculum = Curriculum::findOrFail($request->curriculum_id);

        // Delete existing curriculum subjects for this curriculum
        CurriculumSubject::where('curriculum_id', $curriculum->id)
            ->delete();

        // Create new curriculum subjects
        foreach ($request->subjects as $subjectId) {
            $subject = Subject::findOrFail($subjectId);
            
            CurriculumSubject::create([
                'curriculum_id' => $curriculum->id,
                'subject_code' => $subject->subject_code,
                'subject_description' => $subject->subject_description,
                'year_level' => $subject->year_level,
                'semester' => $subject->semester,
                'is_deleted' => false,
                'is_universal' => true,
            ]);
        }

        return redirect()->route('curriculum.selectSubjects')
            ->with('success', 'Subjects have been successfully imported to the curriculum.');
    }

    public function selectSubjectsChair()
    {
        $curriculums = Curriculum::with('course')
            ->orderByDesc('created_at')
            ->get();

        return view('chairperson.select-curriculum-subjects', compact('curriculums'));
    }

    public function fetchSubjects($curriculum)
    {
        $curriculumId = $curriculum;
        
        $subjects = CurriculumSubject::where('curriculum_id', $curriculumId)
            ->where('is_deleted', false)
            ->with('subject')
            ->get()
            ->map(function ($curriculumSubject) {
                return [
                    'id' => $curriculumSubject->id,
                    'subject_code' => $curriculumSubject->subject_code,
                    'subject_description' => $curriculumSubject->subject_description,
                    'year_level' => $curriculumSubject->year_level,
                    'semester' => $curriculumSubject->semester,
                    'curriculum' => $curriculumSubject->curriculum->name ?? 'N/A',
                    'course' => $curriculumSubject->curriculum->course->course_code ?? 'N/A',
                    'is_universal' => true
                ];
            });

        return response()->json($subjects);
    }
    

}
