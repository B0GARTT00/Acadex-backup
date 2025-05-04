<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Subject;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 🗂 List Activities for an Instructor's Subjects
    public function index(Request $request)
    {
        Gate::authorize('instructor');
    
        $academicPeriodId = session('active_academic_period_id');
    
        $subjects = Subject::where('instructor_id', Auth::id())
            ->where('is_deleted', false)
            ->when($academicPeriodId, fn($q) => $q->where('academic_period_id', $academicPeriodId))
            ->get();
    
        $activities = collect();
    
        if ($request->filled('subject_id')) {
            $subject = Subject::findOrFail($request->subject_id);
    
            if ($academicPeriodId && $subject->academic_period_id !== (int) $academicPeriodId) {
                abort(403, 'This subject does not belong to the current academic period.');
            }
    
            $existing = Activity::where('subject_id', $subject->id)
                ->where('is_deleted', false)
                ->count();
    
            if ($existing === 0) {
                $terms = ['prelim', 'midterm', 'prefinal', 'final'];
                foreach ($terms as $term) {
                    foreach (['quiz' => 3, 'ocr' => 3, 'exam' => 1] as $type => $count) {
                        for ($i = 1; $i <= $count; $i++) {
                            Activity::create([
                                'subject_id' => $subject->id,
                                'term' => $term,
                                'type' => $type,
                                'title' => ucfirst($type) . ' ' . $i,
                                'number_of_items' => 100,
                                'is_deleted' => false,
                                'created_by' => Auth::id(),
                                'updated_by' => Auth::id(),
                            ]);
                        }
                    }
                }
            }
    
            $activities = Activity::where('subject_id', $subject->id)
                ->where('is_deleted', false)
                ->orderBy('term')
                ->orderBy('type')
                ->orderBy('created_at')
                ->get();
        }
    
        return view('instructor.activities.index', compact('subjects', 'activities'));
    }    
    

    // ➕ Full Create Activity Form
    public function create()
    {
        Gate::authorize('instructor');

        $subjects = Subject::where('instructor_id', Auth::id())
            ->where('is_deleted', false)
            ->get();

        $academicPeriods = AcademicPeriod::where('is_deleted', false)
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester')
            ->get();

        return view('instructor.activities.create', compact('subjects', 'academicPeriods'));
    }

    // 🎯 Quick Add Form from inside Manage Grades
    public function addActivity(Request $request)
    {
        Gate::authorize('instructor');
    
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:prelim,midterm,prefinal,final',
        ]);
    
        $subject = Subject::findOrFail($request->subject_id);
        $academicPeriodId = session('active_academic_period_id');
    
        if ($academicPeriodId && $subject->academic_period_id !== (int) $academicPeriodId) {
            abort(403, 'This subject does not belong to the current academic period.');
        }
    
        return view('instructor.activities.add', [
            'subject' => $subject,
            'term' => $request->term
        ]);
    }
    

    // 💾 Store Activity (both standard and inline)
    public function store(Request $request)
    {
        Gate::authorize('instructor');
    
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:prelim,midterm,prefinal,final',
            'type' => 'required|in:quiz,ocr,exam',
            'title' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
        ]);
    
        $subject = Subject::findOrFail($request->subject_id);
        $academicPeriodId = session('active_academic_period_id');
    
        if ($academicPeriodId && $subject->academic_period_id !== (int) $academicPeriodId) {
            abort(403, 'This subject does not belong to the active academic period.');
        }
    
        Activity::create([
            'subject_id' => $subject->id,
            'term' => $request->term,
            'type' => $request->type,
            'title' => $request->title,
            'number_of_items' => $request->points,
            'is_deleted' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    
        return redirect()->route('instructor.grades.index', [
            'subject_id' => $subject->id,
            'term' => $request->term,
        ])->with('success', 'Activity created successfully.');
    }    

    // 🗑 Soft Delete Activity
    public function delete($id)
    {
        Gate::authorize('instructor');

        $activity = Activity::where('id', $id)
            ->where('is_deleted', false)
            ->firstOrFail();

        $activity->update([
            'is_deleted' => true,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Activity deleted successfully.');
    }
}
