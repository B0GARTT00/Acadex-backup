@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold text-dark">📊 GE Coordinator Dashboard</h2>

    {{-- Summary Cards --}}
    <div class="row g-4">
        @php
            $cards = [
                [
                    'label' => 'GE Instructors', 
                    'icon' => '👨‍🏫', 
                    'value' => $instructorCount, 
                    'color' => 'text-primary',
                    'route' => route('ge-coordinator.instructors')
                ],
                [
                    'label' => 'GE Students', 
                    'icon' => '👥', 
                    'value' => $studentCount, 
                    'color' => 'text-info',
                    'route' => route('ge-coordinator.grades')
                ],
                [
                    'label' => 'GE Subjects', 
                    'icon' => '📚', 
                    'value' => $subjectCount, 
                    'color' => 'text-success',
                    'route' => '#'
                ],
                [
                    'label' => 'Pending Approvals', 
                    'icon' => '⏳', 
                    'value' => $pendingInstructors->count(), 
                    'color' => 'text-warning',
                    'route' => route('ge-coordinator.instructors')
                ],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-md-3">
                <a href="{{ $card['route'] }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 rounded-4 p-3 h-100 bg-white animate__animated animate__fadeInUp">
                        <div class="d-flex align-items-center">
                            <span class="me-2 fs-4">{{ $card['icon'] }}</span>
                            <div>
                                <h6 class="text-muted mb-0">{{ $card['label'] }}</h6>
                                <h3 class="fw-bold {{ $card['color'] }} mt-1">{{ $card['value'] }}</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Recent Students --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">👥 Recent GE Students</h5>
                    @if($recentStudents->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Course</th>
                                        <th>Year Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentStudents as $student)
                                        <tr>
                                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                            <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                            <td>{{ $student->year_level }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="{{ route('ge-coordinator.grades') }}" class="btn btn-outline-primary btn-sm">
                                View All Students <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent students found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    .btn-lg {
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
    }
    .table th {
        border-top: none;
        text-transform: uppercase;
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 600;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endpush
@endsection
