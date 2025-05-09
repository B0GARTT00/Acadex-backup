@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4" style="background-color: #EAF8E7; border-radius: 1rem;">
    <h1 class="text-3xl font-bold mb-8 text-gray-800 flex items-center">
        <i class="bi bi-person-lines-fill text-success me-3 fs-2"></i>
        Instructor Account Management
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
            Currently Active Instructors
        </h2>

        @if($instructors->isEmpty())
            <div class="alert alert-warning shadow-sm rounded">No active instructors.</div>
        @else
            <div class="table-responsive bg-white shadow-sm rounded-4 p-3">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Instructor Name</th>
                            <th>Email Address</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instructors as $instructor)
                            <tr>
                                <td>{{ $instructor->last_name }}, {{ $instructor->first_name }} {{ $instructor->middle_name }}</td>
                                <td>{{ $instructor->email }}</td>
                                <td class="text-center">
                                    @php
                                        $request = \App\Models\GESubjectRequest::where('instructor_id', $instructor->id)
                                            ->where('status', 'approved')
                                            ->first();
                                    @endphp
                                    
                                    @if($request)
                                        <span class="badge bg-blue-100 text-blue-800 px-3 py-2 rounded-pill">
                                            GE Instructor
                                        </span>
                                    @endif
                                    
                                    <span class="badge bg-gray-100 text-gray-800 px-3 py-2 rounded-pill">
                                        Department Instructor
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge border border-success text-success px-3 py-2 rounded-pill">
                                        Active
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $request = \App\Models\GESubjectRequest::where('instructor_id', $instructor->id)
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                                    @endphp
                                    
                                    @if($request)
                                        @if($request->status === 'pending')
                                            <span class="badge bg-info px-3 py-2 rounded-pill">
                                                <i class="bi bi-book"></i> GE Request Pending
                                            </span>
                                        @elseif($request->status === 'approved')
                                            <span class="badge bg-blue-100 text-blue-800 px-3 py-2 rounded-pill">
                                                <i class="bi bi-person-check"></i> GE Instructor
                                            </span>
                                        @elseif($request->status === 'rejected')
                                            <span class="badge bg-danger px-3 py-2 rounded-pill">
                                                <i class="bi bi-x-circle"></i> GE Request Rejected
                                            </span>
                                        @endif
                                    @else
                                        @if(!$instructor->is_universal)
                                            <button type="button"
                                                class="btn btn-info btn-sm d-inline-flex align-items-center gap-1 me-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#requestGEModal"
                                                data-instructor-id="{{ $instructor->id }}"
                                                data-instructor-name="{{ $instructor->last_name }}, {{ $instructor->first_name }}">
                                                <i class="bi bi-book"></i> Request GE
                                            </button>
                                        @endif
                                    @endif
                                    <button type="button"
                                        class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeactivateModal"
                                        data-instructor-id="{{ $instructor->id }}"
                                        data-instructor-name="{{ $instructor->last_name }}, {{ $instructor->first_name }}">
                                        <i class="bi bi-person-x-fill"></i> Deactivate
                                    </button>
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
            Pending Account Approvals
        </h2>

        @if($pendingAccounts->isEmpty())
            <div class="alert alert-info shadow-sm rounded">No pending instructor applications.</div>
        @else
            <div class="table-responsive bg-white shadow-sm rounded-4 p-3">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Course</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingAccounts as $account)
                            <tr>
                                <td>{{ $account->last_name }}, {{ $account->first_name }} {{ $account->middle_name }}</td>
                                <td>{{ $account->email }}</td>
                                <td>{{ $account->department->department_description ?? 'N/A' }}</td>
                                <td>{{ $account->course->course_description ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if($account->is_universal)
                                        <span class="badge bg-blue-100 text-blue-800 px-3 py-2 rounded-pill">
                                            GE Instructor
                                        </span>
                                    @else
                                        <span class="badge bg-gray-100 text-gray-800 px-3 py-2 rounded-pill">
                                            Department Instructor
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                        class="btn btn-success btn-sm d-inline-flex align-items-center gap-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmApproveModal"
                                        data-id="{{ $account->id }}"
                                        data-name="{{ $account->last_name }}, {{ $account->first_name }}">
                                        <i class="bi bi-check-circle-fill"></i> Approve
                                    </button>

                                    <button type="button"
                                        class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1 ms-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmRejectModal"
                                        data-id="{{ $account->id }}"
                                        data-name="{{ $account->last_name }}, {{ $account->first_name }}">
                                        <i class="bi bi-x-circle-fill"></i> Reject
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

{{-- Modals --}}
<div class="modal fade" id="requestGEModal" tabindex="-1" aria-labelledby="requestGEModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="requestGEForm">
            @csrf
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="requestGEModalLabel">Request GE Subject</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Requesting GE subject for <strong id="requestGEName"></strong></p>
                    <div class="mb-3">
                        <label for="request_reason" class="form-label">Reason for Requesting GE Subject</label>
                        <textarea class="form-control" id="request_reason" name="request_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Submit Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modals --}}
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

<div class="modal fade" id="confirmApproveModal" tabindex="-1" aria-labelledby="confirmApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="approveForm">
            @csrf
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

<div class="modal fade" id="confirmRejectModal" tabindex="-1" aria-labelledby="confirmRejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="rejectForm">
            @csrf
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmRejectModalLabel">Confirm Rejection</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to reject <strong id="rejectName"></strong>'s account?
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const approveModal = document.getElementById('confirmApproveModal');
    const rejectModal = document.getElementById('confirmRejectModal');
    const deactivateModal = document.getElementById('confirmDeactivateModal');

    if (approveModal) {
        approveModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            document.getElementById('approveForm').action = `/chairperson/approvals/${button.getAttribute('data-id')}/approve`;
            document.getElementById('approveName').textContent = button.getAttribute('data-name');
        });
    }

    if (rejectModal) {
        rejectModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            document.getElementById('rejectForm').action = `/chairperson/approvals/${button.getAttribute('data-id')}/reject`;
            document.getElementById('rejectName').textContent = button.getAttribute('data-name');
        });
    }

    if (deactivateModal) {
        deactivateModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            document.getElementById('deactivateForm').action = `/chairperson/instructors/${button.getAttribute('data-instructor-id')}/deactivate`;
            document.getElementById('instructorName').textContent = button.getAttribute('data-instructor-name');
        });
    }
</script>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestGEModal = new bootstrap.Modal(document.getElementById('requestGEModal'));
    const requestGEForm = document.getElementById('requestGEForm');
    const requestGEName = document.getElementById('requestGEName');

    document.querySelectorAll('[data-bs-target="#requestGEModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const instructorId = this.dataset.instructorId;
            const instructorName = this.dataset.instructorName;
            requestGEName.textContent = instructorName;
            requestGEForm.action = `/chairperson/instructors/${instructorId}/request-ge`;
        });
    });
});
</script>
@endpush

@endsection
