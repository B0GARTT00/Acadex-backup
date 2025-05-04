<div class="d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 250px; background-color: #4da674; min-height: 100vh;">
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-3 text-white text-decoration-none">
        <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 28px; height: 28px;" class="me-2">
        <span class="fs-4 fw-bold">ACADEX</span>
    </a>

    <hr class="border-white-50">

    <!-- Always visible Dashboard link -->
    <ul class="nav nav-pills flex-column mb-4">
        <li>
            <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active bg-success' : '' }}">
                <div class="d-flex align-items-center">
                    <span class="me-2" style="width: 20px;">🏠</span> Dashboard
                </div>
            </a>
        </li>
    </ul>

    @php $role = Auth::user()->role; @endphp

    {{-- Instructor --}}
    @if ($role === 0)
        <h6 class="text-uppercase fw-bold text-white-50 px-2 mb-3">Instructor</h6>
        <ul class="nav nav-pills flex-column mb-4">
            <li>
                <a href="{{ route('instructor.students.index') }}" class="nav-link text-white {{ request()->routeIs('instructor.students.*') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">🎓</span> Manage Students
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.grades.index') }}" class="nav-link text-white {{ request()->routeIs('instructor.grades.*') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📝</span> Manage Grades
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.activities.index') }}" class="nav-link text-white {{ request()->routeIs('instructor.activities.*') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📌</span> Manage Activities
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('instructor.final-grades.index') }}" class="nav-link text-white {{ request()->routeIs('instructor.final-grades.*') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📈</span> Final Grades
                    </div>
                </a>
            </li>
        </ul>
    @endif

    {{-- Chairperson --}}
    @if ($role === 1)
        <h6 class="text-uppercase fw-bold text-white-50 px-2 mb-3">Chairperson</h6>
        <ul class="nav nav-pills flex-column mb-4">
            <li>
                <a href="{{ route('chairperson.instructors') }}" class="nav-link text-white {{ request()->routeIs('chairperson.instructors') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">🧑‍🏫</span> Manage Instructors
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('chairperson.assignSubjects') }}" class="nav-link text-white {{ request()->routeIs('chairperson.assignSubjects') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📚</span> Assign Subjects
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('chairperson.viewGrades') }}" class="nav-link text-white {{ request()->routeIs('chairperson.viewGrades') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📈</span> View Grades
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('chairperson.studentsByYear') }}" class="nav-link text-white {{ request()->routeIs('chairperson.studentsByYear') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">👨‍🎓</span> Students by Year
                    </div>
                </a>
            </li>
        </ul>
    @endif

    {{-- Dean --}}
    @if ($role === 2)
        <h6 class="text-uppercase fw-bold text-white-50 px-2 mb-3">Dean</h6>
        <ul class="nav nav-pills flex-column mb-4">
            <li>
                <a href="{{ route('dean.instructors') }}" class="nav-link text-white {{ request()->routeIs('dean.instructors') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">🧑‍🏫</span> View Instructors
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('dean.students') }}" class="nav-link text-white {{ request()->routeIs('dean.students') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">👨‍🎓</span> View Students
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('dean.grades') }}" class="nav-link text-white {{ request()->routeIs('dean.grades') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📈</span> View Grades
                    </div>
                </a>
            </li>
        </ul>
    @endif

    {{-- Admin --}}
    @if ($role === 3)
        <h6 class="text-uppercase fw-bold text-white-50 px-2 mb-3">Admin</h6>
        <ul class="nav nav-pills flex-column mb-4">
            <li>
                <a href="{{ route('admin.departments') }}" class="nav-link text-white {{ request()->routeIs('admin.departments') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">🏢</span> Departments
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.courses') }}" class="nav-link text-white {{ request()->routeIs('admin.courses') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📘</span> Courses
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.subjects') }}" class="nav-link text-white {{ request()->routeIs('admin.subjects') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📖</span> Subjects
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.academicPeriods') }}" class="nav-link text-white {{ request()->routeIs('admin.academicPeriods') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📅</span> Academic Periods
                    </div>
                </a>
            </li>
        </ul>
    @endif

    <!-- Logout -->
    <hr class="border-white-50 mt-auto">
    <ul class="nav nav-pills flex-column mt-2">
        <li>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="nav-link text-white border-0 bg-transparent p-0">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">🚪</span> Logout
                    </div>
                </button>
            </form>
        </li>
    </ul>
</div>
