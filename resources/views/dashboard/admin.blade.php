@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold text-dark">📊 Admin Dashboard</h2>

    {{-- Summary Cards --}}
    <div class="row g-4">
        @php
            $cards = [
                [
                    'label' => 'Total Departments', 
                    'icon' => '🏢', 
                    'value' => $departments->count(), 
                    'color' => 'text-primary',
                    'route' => route('admin.departments')
                ],
                [
                    'label' => 'Total Courses', 
                    'icon' => '🎓', 
                    'value' => $courses->count(), 
                    'color' => 'text-info',
                    'route' => route('admin.courses')
                ],
                [
                    'label' => 'Total Subjects', 
                    'icon' => '📚', 
                    'value' => $subjects->count(), 
                    'color' => 'text-success',
                    'route' => route('admin.subjects')
                ],
                [
                    'label' => 'Active Academic Period', 
                    'icon' => '📅', 
                    'value' => $activePeriod ? $activePeriod->academic_year . ' - ' . ucfirst($activePeriod->semester) : 'None', 
                    'color' => 'text-warning',
                    'route' => route('admin.academicPeriods')
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

    {{-- Recent Activities --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">📋 Recent Activities</h5>
                    @if($recentActivities->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivities as $activity)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $activity->type === 'department' ? 'primary' : ($activity->type === 'course' ? 'info' : ($activity->type === 'subject' ? 'success' : 'warning')) }}">
                                                    {{ ucfirst($activity->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $activity->description }}</td>
                                            <td>{{ $activity->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                View All Activities <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent activities found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Add animation styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
@endpush

@push('scripts')
<!-- Add any additional scripts if needed -->
@endpush
