@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Manage Activities</h1>

    {{-- Back and Add Button --}}
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('instructor.grades.index') }}" class="text-indigo-600 hover:underline">
            ← Back to Grades
        </a>

        <a href="{{ route('instructor.activities.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
            + Add New Activity
        </a>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('instructor.activities.index') }}" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium mb-1">Select Subject:</label>
                <select name="subject_id" onchange="this.form.submit()" class="w-full border px-3 py-2 rounded">
                    <option value="">-- All Subjects --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_code }} - {{ $subject->subject_description }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Select Term:</label>
                <select name="term" onchange="this.form.submit()" class="w-full border px-3 py-2 rounded">
                    <option value="">-- All Terms --</option>
                    <option value="prelim" {{ request('term') == 'prelim' ? 'selected' : '' }}>Prelim</option>
                    <option value="midterm" {{ request('term') == 'midterm' ? 'selected' : '' }}>Midterm</option>
                    <option value="prefinal" {{ request('term') == 'prefinal' ? 'selected' : '' }}>Prefinal</option>
                    <option value="final" {{ request('term') == 'final' ? 'selected' : '' }}>Final</option>
                </select>
            </div>
        </div>
    </form>

    {{-- Activities Table --}}
    @if(count($activities))
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded shadow">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 border">Title</th>
                        <th class="py-2 px-4 border">Type</th>
                        <th class="py-2 px-4 border">Term</th>
                        <th class="py-2 px-4 border text-center">No. of Items</th>
                        <th class="py-2 px-4 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                        <tr class="hover:bg-gray-100">
                            <td class="py-2 px-4 border">{{ $activity->title }}</td>
                            <td class="py-2 px-4 border capitalize text-center">{{ $activity->type }}</td>
                            <td class="py-2 px-4 border capitalize text-center">{{ $activity->term }}</td>
                            <td class="py-2 px-4 border text-center">{{ $activity->number_of_items }}</td>
                            <td class="py-2 px-4 border text-center">
                                <button type="button"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal"
                                        data-activity-id="{{ $activity->id }}"
                                        data-activity-title="{{ $activity->title }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center text-gray-500 mt-8">
            <p>No activities found for selected subject/term.</p>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="deleteActivityForm">
      @csrf
      @method('DELETE')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete the activity: <strong id="activityTitlePlaceholder">this activity</strong>?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
    const deleteModal = document.getElementById('confirmDeleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const activityId = button.getAttribute('data-activity-id');
        const activityTitle = button.getAttribute('data-activity-title');

        const form = deleteModal.querySelector('#deleteActivityForm');
        form.action = `/instructor/activities/${activityId}`;
        deleteModal.querySelector('#activityTitlePlaceholder').textContent = activityTitle;
    });
</script>
@endpush
@endsection
