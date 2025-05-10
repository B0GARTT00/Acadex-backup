@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">
        <i class="bi bi-person-badge text-success me-2"></i>
        Assign Subjects to Instructors
    </h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($subjects->count())
        <div class="bg-white shadow rounded-4 overflow-x-auto">
            <table class="table table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Subject Code</th>
                        <th>Description</th>
                        <th>Assigned Instructor</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->subject_code }}</td>
                            <td>{{ $subject->subject_description }}</td>
                            <td>{{ $subject->instructor ? $subject->instructor->name : '—' }}</td>
                            <td class="text-center">
                                @if ($subject->instructor)
                                    <button
                                        onclick="openConfirmUnassignModal({{ $subject->id }}, '{{ addslashes($subject->subject_code . ' - ' . $subject->subject_description) }}')"
                                        class="btn btn-danger btn-sm">
                                        <i class="bi bi-x-circle me-1"></i> Unassign
                                    </button>
                                @else
                                    <button
                                        onclick="openConfirmAssignModal({{ $subject->id }}, '{{ addslashes($subject->subject_code . ' - ' . $subject->subject_description) }}')"
                                        class="btn btn-success shadow-sm">
                                        <i class="bi bi-person-plus me-1"></i> Assign
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center text-muted mt-5 bg-warning bg-opacity-25 border border-warning rounded py-4 px-6">
            No subjects available for this academic period.
        </div>
    @endif
</div>

{{-- Confirm Unassign Modal --}}
<div id="confirmUnassignModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white w-full max-w-lg rounded-4 shadow-lg overflow-hidden flex flex-col">
        <div class="bg-danger text-white px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Unassign
            </h5>
            <button onclick="closeConfirmUnassignModal()" class="btn-close btn-close-white" aria-label="Close"></button>
        </div>

        <div class="p-4">
            <p>Are you sure you want to unassign this subject? This action cannot be undone.</p>
            <form id="unassignForm" action="{{ route('chairperson.toggleAssignedSubject') }}" method="POST">
                @csrf
                <input type="hidden" name="subject_id" id="unassign_subject_id">
                <input type="hidden" name="instructor_id" value="">
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i> Unassign
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeConfirmUnassignModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Confirm Assign Modal --}}
<div id="confirmAssignModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white w-full max-w-lg rounded-4 shadow-lg overflow-hidden flex flex-col">
        <div class="bg-success text-white px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-check-circle-fill me-2"></i> Confirm Assign
            </h5>
            <button onclick="closeConfirmAssignModal()" class="btn-close btn-close-white" aria-label="Close"></button>
        </div>

        <div class="p-4">
            <p>Are you sure you want to assign this subject to an instructor?</p>
            <form id="assignForm" method="POST" action="{{ route('chairperson.toggleAssignedSubject') }}" class="vstack gap-3">
                @csrf
                <input type="hidden" name="subject_id" id="assign_subject_id">
                <input type="hidden" name="instructor_id" id="assign_instructor_id">
                <div>
                    <label class="form-label">Select Instructor</label>
                    <select name="instructor_id" class="form-select" required>
                        <option value="">-- Choose Instructor --</option>
                        @foreach ($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Assign
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeConfirmAssignModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Open confirm unassign modal and populate fields
    function openConfirmUnassignModal(subjectId, subjectName) {
        document.getElementById('unassign_subject_id').value = subjectId;
        const modal = document.getElementById('confirmUnassignModal');
        modal.classList.remove('hidden');
        modal.classList.add('d-flex');
    }

    // Close confirm unassign modal
    function closeConfirmUnassignModal() {
        const modal = document.getElementById('confirmUnassignModal');
        modal.classList.add('hidden');
        modal.classList.remove('d-flex');
    }

    // Open confirm assign modal and populate fields
    function openConfirmAssignModal(subjectId, subjectName) {
        document.getElementById('assign_subject_id').value = subjectId;
        const modal = document.getElementById('confirmAssignModal');
        modal.classList.remove('hidden');
        modal.classList.add('d-flex');
    }

    // Close confirm assign modal
    function closeConfirmAssignModal() {
        const modal = document.getElementById('confirmAssignModal');
        modal.classList.add('hidden');
        modal.classList.remove('d-flex');
    }
</script>
@endpush
@endsection
