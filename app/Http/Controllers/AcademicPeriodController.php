<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AcademicPeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 📘 View all academic periods
    public function index()
    {
        Gate::authorize('admin');

        $periods = AcademicPeriod::where('is_deleted', false)
            ->orderByDesc('academic_year')
            ->orderByRaw("FIELD(semester, '1st', '2nd', 'Summer')")
            ->get();

        return view('admin.academic-periods.index', compact('periods'));
    }

    // 📝 Show form to create academic period
    public function create()
    {
        Gate::authorize('admin');

        return view('admin.academic-periods.create');
    }

    // 📦 Store new academic period
    public function store(Request $request)
    {
        Gate::authorize('admin');

        $validated = $request->validate([
            'academic_year' => 'required|string|regex:/^[0-9]{4}-[0-9]{4}$/|unique:academic_periods,academic_year',
            'semester' => 'required|string|in:1st,2nd,Summer',
            'is_active' => 'required|boolean',
        ]);

        AcademicPeriod::create([
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
            'is_active' => $validated['is_active'],
            'is_deleted' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.academicPeriods.index')->with('success', 'Academic period created successfully.');
    }

    // 🔄 Auto-generate next academic year
    public function generate()
    {
        Gate::authorize('admin');

        $latest = AcademicPeriod::orderBy('created_at', 'desc')->first();
        $currentYear = now()->year;

        if ($latest && preg_match('/^\d{4}-\d{4}$/', $latest->academic_year)) {
            [$startYear, $endYear] = explode('-', $latest->academic_year);
            $startYear = intval($startYear) + 1;
            $endYear = intval($endYear) + 1;
        } else {
            $startYear = $currentYear;
            $endYear = $currentYear + 1;
        }

        $newAcademicYear = "{$startYear}-{$endYear}";

        $alreadyExists = AcademicPeriod::where('academic_year', $newAcademicYear)->count() >= 2 &&
                         AcademicPeriod::where('academic_year', $startYear)->where('semester', 'Summer')->exists();

        if (!$alreadyExists) {
            AcademicPeriod::insert([
                [
                    'academic_year' => $newAcademicYear,
                    'semester' => '1st',
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'academic_year' => $newAcademicYear,
                    'semester' => '2nd',
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'academic_year' => $startYear,
                    'semester' => 'Summer',
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        return redirect()->route('admin.academicPeriods.index')->with('success', 'New academic periods generated successfully.');
    }
}
