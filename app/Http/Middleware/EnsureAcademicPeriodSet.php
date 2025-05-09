<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAcademicPeriodSet
{
    public function handle(Request $request, Closure $next)
    {
        // Check if academic period is required for this user
        $requiresAcademicPeriod = false;
        
        if (Auth::check()) {
            $user = Auth::user();
            
            // Instructor, Chairperson, or GE Coordinator always require academic period
            if (in_array($user->role, [0, 2, 4])) {
                $requiresAcademicPeriod = true;
            }
        }

        // If academic period is required but not set, redirect to selection
        if (
            $requiresAcademicPeriod &&
            !session()->has('active_academic_period_id') &&
            !$request->is('select-academic-period') &&
            !$request->is('set-academic-period')
        ) {
            return redirect()->route('select.academicPeriod');
        }

        return $next($request);
    }
}
