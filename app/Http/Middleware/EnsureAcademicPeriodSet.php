<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAcademicPeriodSet
{
    public function handle(Request $request, Closure $next)
    {
        if (
            Auth::check() &&
            in_array(Auth::user()->role, [0, 4]) && // Instructor or GE Coordinator
            !session()->has('active_academic_period_id') &&
            !$request->is('select-academic-period') &&
            !$request->is('set-academic-period')
        ) {
            return redirect()->route('select.academicPeriod');
        }

        return $next($request);
    }
}
