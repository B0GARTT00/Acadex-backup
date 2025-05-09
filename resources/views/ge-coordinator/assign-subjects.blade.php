@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">
        <i class="bi bi-book text-success me-2"></i>
        Assign GE Subjects to Instructors
    </h1>

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
                                <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm" disabled>
                                    <i class="bi bi-check-circle me-1"></i> Assigned
                                </button>
                                @else
                                    <button
                                        onclick="openAssignModal({{ $subject->id }}, '{{ addslashes($subject->subject_code . ' - ' . $subject->subject_description) }}')"
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
            No GE subjects available for this academic period.
        </div>
    @endif
</div>

{{-- Assign Modal --}}
<div id="assignModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white w-full max-w-lg rounded-4 shadow-lg overflow-hidden flex flex-col">
        <!-- Green Header -->
        <div class="bg-success text-white px-4 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-person-check-fill me-2"></i> Assign Instructor to GE Subject
            </h5>
            <button onclick="closeAssignModal()" class="btn-close btn-close-white" aria-label="Close"></button>
        </div>

        <!-- Modal Body -->
        <div class="p-4">
            <form method="POST" action="{{ route('ge-coordinator.store.assigned.subject') }}" class="vstack gap-3">
                @csrf
                <input type="hidden" name="subject_id" id="modal_subject_id">

                <div>
                    <label class="form-label">GE Subject</label>
                    <input type="text" id="modal_subject_name" class="form-control bg-light" disabled>
                </div>

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
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openAssignModal(subjectId, subjectName) {
        document.getElementById('modal_subject_id').value = subjectId;
        document.getElementById('modal_subject_name').value = subjectName;
        const modal = document.getElementById('assignModal');
        modal.classList.remove('hidden');
        modal.classList.add('d-flex');
    }

    function closeAssignModal() {
        const modal = document.getElementById('assignModal');
        modal.classList.add('hidden');
        modal.classList.remove('d-flex');
    }

    // Close modal when clicking outside
    document.getElementById('assignModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAssignModal();
        }
    });
</script>
@endpush
@endsection
