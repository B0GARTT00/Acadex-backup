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
                    'route' => route('ge-coordinator.subjects.index')
                ],

                [
                    'label' => 'Pending Approvals', 
                    'icon' => '⏳', 
                    'value' => $pendingInstructors->count(), 
                    'color' => 'text-warning',
                    'route' => route('ge-coordinator.instructors')
                ]
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

        {{-- GE Subject Requests --}}
        <div class="col-md-8 mx-auto mt-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4 text-center">📝 GE Subject Requests</h5>
                    @if($pendingRequests->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Instructor</th>
                                        <th>Department</th>
                                        <th>Request Reason</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $request)
                                        <tr>
                                            <td>{{ $request->instructor->last_name }}, {{ $request->instructor->first_name }}</td>
                                            <td>{{ $request->department->name }}</td>
                                            <td>{{ $request->request_reason }}</td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <form action="{{ route('ge-coordinator.ge-requests.approve', $request->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('ge-coordinator.ge-requests.reject', $request->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Reject" onclick="return confirm('Are you sure you want to reject this request?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('ge-coordinator.ge-requests') }}" class="btn btn-primary btn-sm">
                                View All Requests
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">No pending GE subject requests</p>
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
@push('scripts')
<script>
    function approveRequest(requestId) {
        if (confirm('Are you sure you want to approve this request?')) {
            $.ajax({
                url: '{{ route('ge-coordinator.ge-requests.approve', ':id') }}'.replace(':id', requestId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Request approved successfully');
                        location.reload();
                    } else {
                        toastr.error('Failed to approve request');
                    }
                },
                error: function() {
                    toastr.error('An error occurred while processing the request');
                }
            });
        }
    }

    function rejectRequest(requestId) {
        if (confirm('Are you sure you want to reject this request?')) {
            $.ajax({
                url: '{{ route('ge-coordinator.ge-requests.reject', ':id') }}'.replace(':id', requestId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Request rejected successfully');
                        location.reload();
                    } else {
                        toastr.error('Failed to reject request');
                    }
                },
                error: function() {
                    toastr.error('An error occurred while processing the request');
                }
            });
        }
    }
</script>
@endpush

@endsection
