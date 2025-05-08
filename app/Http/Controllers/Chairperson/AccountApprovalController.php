<?php

namespace App\Http\Controllers\Chairperson;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\UnverifiedUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountApprovalController extends Controller
{
    /**
     * Display a list of all pending instructor accounts for approval.
     */
    public function index(): View
    {
        // Get the current user's department
        $userDepartmentId = auth()->user()->department_id;
        
        // If user is GE Coordinator, show only GE instructors
        if (auth()->user()->role === 4) { // GE Coordinator role
            $pendingAccounts = UnverifiedUser::where('is_universal', true)
                ->with(['department', 'course'])
                ->whereNull('approved_at')
                ->whereNull('rejected_at')
                ->get();
                
            return view('ge-coordinator.manage-instructors', [
                'pendingInstructors' => $pendingAccounts,
                'instructors' => collect([]),
                'departments' => Department::all(),
                'courses' => Course::all()
            ]);
        } else {
            // For department chairpersons, show only their department's non-GE instructors
            $pendingAccounts = UnverifiedUser::where('is_universal', false)
                ->where('department_id', $userDepartmentId)
                ->whereNull('approved_at')
                ->whereNull('rejected_at')
                ->with(['department', 'course'])
                ->get();
                
            return view('chairperson.manage-instructors', [
                'pendingAccounts' => $pendingAccounts,
                'instructors' => User::where('role', 0)
                    ->where('department_id', $userDepartmentId)
                    ->where('is_universal', false)
                    ->orderBy('last_name')
                    ->get()
            ]);
        }
    }

    /**
     * Approve a pending instructor and migrate their data to the main users table.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function approve(int $id): RedirectResponse
    {
        $pending = UnverifiedUser::findOrFail($id);
        
        // Check if the current user is authorized to approve this instructor
        if ((auth()->user()->role === 4 && !$pending->is_universal) || // GE Coordinator trying to approve non-GE
            (auth()->user()->role !== 4 && $pending->is_universal)) {  // Department chair trying to approve GE
            return back()->with('error', 'You are not authorized to approve this instructor.');
        }

        // Check if already approved
        if ($pending->approved_at) {
            return back()->with('error', 'This account has already been approved.');
        }


        // Transfer to the main users table
        $user = User::create([
            'first_name'    => $pending->first_name,
            'middle_name'   => $pending->middle_name,
            'last_name'     => $pending->last_name,
            'email'         => $pending->email,
            'password'      => $pending->password, // Already hashed
            'department_id' => $pending->department_id,
            'course_id'     => $pending->course_id,
            'role'          => 0, // Instructor role
            'is_active'     => true,
            'is_universal'  => $pending->is_universal, // Preserve GE status
            'email_verified_at' => now(),
        ]);

        // Mark as approved
        $pending->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Remove from unverified list
        $pending->delete();
        
        $message = $user->is_universal 
            ? 'GE Instructor account has been approved successfully.'
            : 'Instructor account has been approved successfully.';
            
        return back()->with('success', $message);
    }

    /**
     * Reject and delete a pending instructor account request.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function reject(int $id): RedirectResponse
    {
        $pending = UnverifiedUser::findOrFail($id);
        
        // Check if already approved
        if ($pending->approved_at) {
            return back()->with('error', 'This account has already been approved.');
        }
        
        // Mark as rejected
        $pending->update([
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);
        
        // Remove from unverified list
        $pending->delete();

        $message = $pending->is_universal 
            ? 'GE Instructor account request has been rejected.'
            : 'Instructor account request has been rejected.';
            
        return back()->with('success', $message);
    }
}
