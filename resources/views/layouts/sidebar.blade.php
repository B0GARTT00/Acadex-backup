@php $role = Auth::user()->role; @endphp
<div class="d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 250px; background-color: #259c59; min-height: 100vh;">
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-3 text-white text-decoration-none">
        <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 28px; height: 28px;" class="me-2">
        <span class="fs-4 fw-bold">ACADEX</span>
    </a>

    <hr class="border-white-50">

    <!-- Dashboard link -->
    <ul class="nav nav-pills flex-column mb-4">
        <li>
            @if($role === 0) <!-- Instructor -->
                <a href="{{ route('instructor.dashboard') }}" class="nav-link text-white {{ request()->routeIs('instructor.dashboard') ? 'active bg-success' : '' }}">
            @elseif($role === 1) <!-- Chairperson -->
                <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active bg-success' : '' }}">
            @elseif($role === 4) <!-- GE Coordinator -->
                <a href="{{ route('ge-coordinator.dashboard') }}" class="nav-link text-white {{ request()->routeIs('ge-coordinator.dashboard') ? 'active bg-success' : '' }}">
            @else <!-- Default/Admin -->
                <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active bg-success' : '' }}">
            @endif
                <div class="d-flex align-items-center">
                    <span class="me-2" style="width: 20px;">🏠</span> Dashboard
                </div>
            </a>
        </li>
    </ul>



    {{-- Instructor --}}
    @if ($role === 0)
        <h6 class="text-uppercase fw-bold text-white-50 px-2 mb-3">Instructor</h6>
        <ul class="nav nav-pills flex-column mb-4">
            <li>
                <a href="{{ route('instructor.students.index') }}"
                class="nav-link text-white {{ request()->routeIs('instructor.students.index') ? 'active bg-success' : '' }}">
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

        <!-- Import Section for Instructor -->
        <hr class="border-white-50">
        <h6 class="text-uppercase fw-bold text-white-50 px-2 mb-3">Import Section</h6>
        <ul class="nav nav-pills flex-column mb-4">
            <li>
                <a href="{{ route('instructor.students.import') }}" class="nav-link text-white {{ request()->routeIs('instructor.students.import') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📤</span> Import Student
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

        <!-- Imports Section for Chairperson -->
        <hr class="border-white-50">
        <h6 class="text-uppercase fw-bold text-white-50 px-2 mb-3">Import Section</h6>
        <ul class="nav nav-pills flex-column mb-4">
            <li>
                <a href="{{ route('curriculum.selectSubjects') }}" class="nav-link text-white {{ request()->routeIs('curriculum.selectSubjects') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📥</span> Import Subjects
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

    {{-- GE Coordinator --}}
    @if ($role === 4)
        <h6 class="text-uppercase fw-bold text-white-50 px-2 mb-3">GE Coordinator</h6>
        <ul class="nav nav-pills flex-column mb-4">
            <li>
                <a href="{{ route('ge-coordinator.instructors') }}" class="nav-link text-white {{ request()->routeIs('ge-coordinator.instructors*') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">👥</span> Manage GE Instructors
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('ge-coordinator.assign.subjects') }}" class="nav-link text-white {{ request()->routeIs('ge-coordinator.assign.subjects*') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📚</span> Assign GE Subjects
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('ge-coordinator.grades') }}" class="nav-link text-white {{ request()->routeIs('ge-coordinator.grades*') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📈</span> View GE Grades
                    </div>
                </a>
            </li>
            <li>
                <a href="{{ route('ge-coordinator.subjects.import') }}" class="nav-link text-white {{ request()->routeIs('ge-coordinator.subjects.import*') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">📥</span> Import GE Subjects
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
                <a href="{{ route('admin.users') }}" class="nav-link text-white {{ request()->routeIs('admin.users') ? 'active bg-success' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width: 20px;">👤</span> Users
                    </div>
                </a>
            </li>
        </ul>
    @endif
</div>
