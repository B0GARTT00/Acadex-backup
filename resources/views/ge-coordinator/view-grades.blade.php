@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">View GE Student Grades</h4>
                        <a href="{{ route('ge-coordinator.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('ge-coordinator.grades') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="year_level" class="form-label">Year Level</label>
                                    <select name="year_level" id="year_level" class="form-select">
                                        <option value="">All Years</option>
                                        @for($i = 1; $i <= 4; $i++)
                                            <option value="{{ $i }}" {{ request('year_level') == $i ? 'selected' : '' }}>{{ $i }} Year</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="academic_period_id" class="form-label">Academic Period</label>
                                    <select name="academic_period_id" id="academic_period_id" class="form-select">
                                        <option value="">All Periods</option>
                                        @foreach(\App\Models\AcademicPeriod::orderBy('academic_year', 'desc')->get() as $period)
                                            <option value="{{ $period->id }}" {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                                                {{ $period->academic_year }} - {{ $period->semester }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('ge-coordinator.grades') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Course</th>
                                    <th>Year Level</th>
                                    <th>Subjects</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>{{ $student->year_level }} Year</td>
                                        <td>
                                            @php
                                                $geSubjects = $student->subjects->where('is_universal', true);
                                            @endphp
                                            @if($geSubjects->isNotEmpty())
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($geSubjects as $subject)
                                                        <li>{{ $subject->subject_code }} - {{ $subject->subject_name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                No GE subjects
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-primary">View Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No students found in GE subjects.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
