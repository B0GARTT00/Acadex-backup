@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4" style="background-color: #EAF8E7; border-radius: 1rem;">
    <h1 class="text-3xl font-bold mb-8 text-gray-800 flex items-center">
        <i class="bi bi-person-lines-fill text-success me-3 fs-2"></i>
        GE Instructor Management
    </h1>

    @if(session('status'))
        <div class="alert alert-success shadow-sm rounded">
            {{ session('status') }}
        </div>
    @endif

    {{-- Active Instructors --}}
    <section class="mb-5">
        <h2 class="text-xl font-semibold mb-3 text-gray-700 flex items-center">
            <i class="bi bi-people-fill text-primary me-2 fs-5"></i>
            Currently Active GE Instructors
        </h2>

        @if($instructors->isEmpty())
            <div class="alert alert-warning shadow-sm rounded">No active GE instructors found.</div>
        @else
            <div class="table-responsive bg-white shadow-sm rounded-4 p-3">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Instructor Name</th>
                            <th>Email Address</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instructors as $instructor)
                            <tr>
                                <td>{{ $instructor->last_name }}, {{ $instructor->first_name }} {{ $instructor->middle_name ?? '' }}</td>
                                <td>{{ $instructor->email }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $instructor->is_active ? 'border border-success text-success' : 'border border-secondary text-secondary' }} px-3 py-2 rounded-pill">
                                        {{ $instructor->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="#" 
                                       class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 me-1"
                                       onclick="alert('View functionality not implemented yet')">
                                        <i class="bi bi-eye-fill"></i> View
                                    </a>
                                    @if($instructor->is_active)
                                    <form action="{{ route('ge-coordinator.instructors.deactivate', $instructor->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1"
                                                onclick="return confirm('Are you sure you want to deactivate this instructor?')">
                                            <i class="bi bi-person-dash-fill"></i> Deactivate
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Pending Account Approvals --}}
    @if($pendingInstructors->isNotEmpty())
        <section class="mt-4">
            <h2 class="text-xl font-semibold mb-3 text-gray-700 flex items-center">
                <i class="bi bi-person-check-fill text-warning me-2 fs-5"></i>
                Pending Instructor Approvals
            </h2>

            <div class="table-responsive bg-white shadow-sm rounded-4 p-3">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Applicant Name</th>
                            <th>Email Address</th>
                            <th>Department</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingInstructors as $pending)
                            <tr>
                                <td>{{ $pending->last_name }}, {{ $pending->first_name }} {{ $pending->middle_name ?? '' }}</td>
                                <td>{{ $pending->email }}</td>
                                <td>{{ $pending->department->department_name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <form action="{{ route('ge-coordinator.instructors.approve', $pending) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-check-circle-fill"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('ge-coordinator.instructors.reject', $pending) }}" method="POST" class="d-inline ms-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1"
                                                onclick="return confirm('Are you sure you want to reject this application?')">
                                            <i class="bi bi-x-circle-fill"></i> Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmRejectModal" tabindex="-1" aria-labelledby="confirmRejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmRejectModalLabel">Confirm Rejection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to reject this instructor application?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="rejectForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rejectModal = document.getElementById('confirmRejectModal');
        if (rejectModal) {
            rejectModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const rejectUrl = button.getAttribute('data-reject-url');
                const form = rejectModal.querySelector('#rejectForm');
                form.action = rejectUrl;
            });
        }
    });
</script>
@endpush

@endsection
