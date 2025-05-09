<?php

namespace App\Http\Controllers;

use App\Models\GESubjectRequest;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class GESubjectRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $instructorId)
    {
        Gate::authorize('chairperson');

        $request->validate([
            'request_reason' => 'required|string|max:1000',
        ]);

        GESubjectRequest::create([
            'instructor_id' => $instructorId,
            'department_id' => Auth::user()->department_id,
            'chairperson_id' => Auth::id(),
            'request_reason' => $request->request_reason,
        ]);

        return redirect()->back()->with('success', 'GE subject request submitted successfully');
    }

    public function index()
    {
        Gate::authorize('ge-coordinator');

        $requests = GESubjectRequest::with(['instructor', 'department', 'chairperson'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ge-coordinator.ge-requests', compact('requests'));
    }

    public function approve($id)
    {
        Gate::authorize('ge-coordinator');

        $request = GESubjectRequest::findOrFail($id);
        $request->update([
            'status' => 'approved',
            'reason' => 'Approved by GE Coordinator',
        ]);

        // Update the instructor to be a GE instructor
        $instructor = User::findOrFail($request->instructor_id);
        $instructor->update(['is_universal' => true]);

        return redirect()->back()->with('success', 'Request approved successfully');
    }

    public function reject(Request $request, $id)
    {
        Gate::authorize('ge-coordinator');

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $gesRequest = GESubjectRequest::findOrFail($id);
        $gesRequest->update([
            'status' => 'rejected',
            'reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Request rejected successfully');
    }
}
