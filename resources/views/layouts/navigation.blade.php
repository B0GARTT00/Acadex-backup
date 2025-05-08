<header class="px-6 py-4 shadow-md flex justify-between items-center" style="background-color: #023336; color: white;">
    <!-- Left: Current Academic Period -->
    <h1 class="text-lg font-semibold">
        @php
            $activePeriod = \App\Models\AcademicPeriod::find(session('active_academic_period_id'));
        @endphp
        @if($activePeriod)
            AY {{ $activePeriod->academic_year }} {{ $activePeriod->semester }} Semester
        @else
            Dashboard
        @endif
    </h1>

    <!-- Right: Profile & Notifications -->
    <div class="flex items-center gap-4">

        <div class="flex items-center gap-2">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}"
                 alt="avatar"
                 class="h-8 w-8 rounded-full object-cover">
            <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
        </div>
    </div>
</header>
