<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Always clear any previous session academic period
        Session::forget('active_academic_period_id');

        $user = Auth::user();

        // Redirect based on user role
        switch ($user->role) {
            case 0: // Instructor
            case 2: // Chairperson
                return redirect()->route('select.academicPeriod');
            case 3: // Dean
                return redirect()->route('dean.dashboard');
            case 4: // GE Coordinator
                return redirect()->route('ge-coordinator.dashboard');
            case 5: // Admin
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
