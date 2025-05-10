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
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instructors as $instructor)
                            <tr>
                                <td>{{ $instructor->last_name }}, {{ $instructor->first_name }} {{ $instructor->middle_name ?? '' }}</td>
                                <td>{{ $instructor->email }}</td>
                                <td>GE Instructor</td>
                                <td>
                                    <span class="badge {{ $instructor->is_active ? 'bg-success' : 'bg-secondary' }} text-white px-3 py-1 rounded-pill">
                                        {{ $instructor->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($instructor->is_active)
                                            <button type="button"
                                                class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmDeactivateModal"
                                                data-instructor-id="{{ $instructor->id }}"
                                                data-instructor-name="{{ $instructor->last_name }}, {{ $instructor->first_name }}">
                                                <i class="bi bi-person-dash-fill"></i> Deactivate
                                            </button>
                                        @else
                                            <button type="button"
                                                class="btn btn-success btn-sm d-inline-flex align-items-center gap-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmActivateModal"
                                                data-instructor-id="{{ $instructor->id }}"
                                                data-instructor-name="{{ $instructor->last_name }}, {{ $instructor->first_name }}">
                                                <i class="bi bi-person-check-fill"></i> Activate
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Pending Account Approvals --}}
    <section class="mt-4">
        <h2 class="text-xl font-semibold mb-3 text-gray-700 flex items-center">
            <i class="bi bi-person-check-fill text-warning me-2 fs-5"></i>
            Pending Instructor Approvals
        </h2>

        @if($pendingInstructors->isNotEmpty())
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
                                    <button type="button"
                                            class="btn btn-success btn-sm d-inline-flex align-items-center gap-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmApproveModal"
                                            data-id="{{ $pending->id }}"
                                            data-name="{{ $pending->last_name }}, {{ $pending->first_name }} {{ $pending->middle_name ?? '' }}">
                                        <i class="bi bi-check-circle-fill"></i> Approve
                                    </button>
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
        @else
            <div class="alert alert-info shadow-sm rounded-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <span>There are currently no pending instructor approvals.</span>
                </div>
            </div>
        @endif
    </section>

    {{-- Deactivate Confirmation Modal --}}
    <div class="modal fade" id="confirmDeactivateModal" tabindex="-1" aria-labelledby="confirmDeactivateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="deactivateForm" method="POST">
                @csrf
                <div class="modal-content rounded-4 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="confirmDeactivateModalLabel">Confirm Account Deactivation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to deactivate <strong id="instructorName"></strong>'s account?
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Deactivate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Activate Confirmation Modal --}}
    <div class="modal fade" id="confirmActivateModal" tabindex="-1" aria-labelledby="confirmActivateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="activateForm" method="POST">
                @csrf
                <div class="modal-content rounded-4 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="confirmActivateModalLabel">Confirm Account Activation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to activate <strong id="activateName"></strong>'s account?
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Activate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Approve Confirmation Modal --}}
<div class="modal fade" id="confirmApproveModal" tabindex="-1" aria-labelledby="confirmApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="approveForm">
            @csrf
            <input type="hidden" name="_method" value="POST">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="confirmApproveModalLabel">Confirm Approval</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to approve <strong id="approveName"></strong>'s account?
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const approveModal = document.getElementById('confirmApproveModal');
    const deactivateModal = document.getElementById('confirmDeactivateModal');
    const activateModal = document.getElementById('confirmActivateModal');

    if (approveModal) {
        approveModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            document.getElementById('approveForm').action = `/ge-coordinator/instructors/${button.getAttribute('data-id')}/approve`;
            document.getElementById('approveName').textContent = button.getAttribute('data-name');
        });
    }

    if (deactivateModal) {
        deactivateModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const form = document.getElementById('deactivateForm');
            form.action = `/ge-coordinator/instructors/${button.getAttribute('data-instructor-id')}/deactivate`;
            document.getElementById('instructorName').textContent = button.getAttribute('data-instructor-name');
        });
    }

    if (activateModal) {
        activateModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const form = document.getElementById('activateForm');
            form.action = `/ge-coordinator/instructors/${button.getAttribute('data-instructor-id')}/activate`;
            document.getElementById('activateName').textContent = button.getAttribute('data-instructor-name');
        });
    }
</script>
@endpush
@endsection
