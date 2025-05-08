@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Chairperson Dashboard</h1>
    
    <div class="row mb-4">
        <!-- Instructor Stats -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-primary">Instructors</h6>
                            <h2 class="mb-0">{{ $fullTimeCount + $partTimeCount }}</h2>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                    <div class="mt-2">
                        <span class="text-success">{{ $fullTimeCount }} Full-time</span> | 
                        <span class="text-info">{{ $partTimeCount }} Part-time</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Courses -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-success">Total Courses</h6>
                            <h2 class="mb-0">{{ $totalCourses }}</h2>
                        </div>
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Students per Course -->
    <div class="card mb-4">
        <div class="card-header">
            <h6>Students per Course</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Number of Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coursesWithStudents as $course)
                            <tr>
                                <td>{{ $course->course_code }} - {{ $course->course_description }}</td>
                                <td>{{ $course->students_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No courses found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Students per Year Level -->
    <div class="card">
        <div class="card-header">
            <h6>Students per Year Level</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Year Level</th>
                            <th>Number of Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($yearLevels as $level => $count)
                            <tr>
                                <td>Year {{ $level }}</td>
                                <td>{{ $count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No student data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    h1, h6 {
        color: #4e73df;
    }
</style>
@endpush
@endsection
