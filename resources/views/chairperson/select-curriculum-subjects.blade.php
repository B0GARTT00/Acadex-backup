@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <h1 class="text-2xl font-bold mb-4">
        <i class="bi bi-book-half text-success me-2"></i>
        Confirm Curriculum Subjects
    </h1>

    {{-- Curriculum Dropdown --}}
    <div class="mb-4">
        <label for="curriculumSelect" class="form-label fw-semibold">Select Curriculum</label>
        <select id="curriculumSelect" class="form-select shadow-sm">
            <option value="">-- Choose Curriculum --</option>
            @foreach($curriculums as $curriculum)
                <option value="{{ $curriculum->id }}">
                    {{ $curriculum->name }} ({{ $curriculum->course->course_code }})
                </option>
            @endforeach
        </select>
    </div>

    {{-- Load Button --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <button id="loadSubjectsBtn" class="btn btn-success d-none">
            <span id="loadBtnText"><i class="bi bi-arrow-repeat me-1"></i> Load Subjects</span>
            <span id="loadBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    </div>

    {{-- Subject Selection Form --}}
    <form method="POST" action="{{ route('curriculum.confirmSubjects') }}" id="confirmForm">
        @csrf
        <input type="hidden" name="curriculum_id" id="formCurriculumId">

        <div class="table-responsive d-none" id="subjectsContainer">
            {{-- Tabs for Year Levels --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ul class="nav nav-tabs" id="yearTabs" style="margin-bottom: 0;"></ul>
                <button type="button" class="btn btn-success btn-sm" id="selectAllBtn" data-selected="false">
                    <i class="bi bi-check2-square me-1"></i> Select All
                </button>
            </div>

            <div class="tab-content mt-3" id="subjectsTableBody"></div>

            <div class="text-end mt-3">
                <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#confirmModal">
                    <i class="bi bi-check-circle me-1"></i> Confirm Selected Subjects
                </button>
            </div>
        </div>
    </form>

    {{-- Toast Notification --}}
    @if(session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div class="toast align-items-center text-bg-success show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Submission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to confirm and save the selected subjects for this curriculum?
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submitConfirmBtn" class="btn btn-success">Yes, Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const curriculumSelect = document.getElementById('curriculumSelect');
    const loadSubjectsBtn = document.getElementById('loadSubjectsBtn');
    const subjectsContainer = document.getElementById('subjectsContainer');
    const subjectsTableBody = document.getElementById('subjectsTableBody');
    const formCurriculumId = document.getElementById('formCurriculumId');
    const loadBtnText = document.getElementById('loadBtnText');
    const loadBtnSpinner = document.getElementById('loadBtnSpinner');
    const yearTabs = document.getElementById('yearTabs');
    const selectAllBtn = document.getElementById('selectAllBtn');

    curriculumSelect.addEventListener('change', function () {
        loadSubjectsBtn.classList.toggle('d-none', !this.value);
        subjectsContainer.classList.add('d-none');
        yearTabs.innerHTML = '';
        subjectsTableBody.innerHTML = '';
    });

    loadSubjectsBtn.addEventListener('click', function () {
        const curriculumId = curriculumSelect.value;
        if (!curriculumId) return;

        formCurriculumId.value = curriculumId;
        yearTabs.innerHTML = '';
        subjectsTableBody.innerHTML = '';
        loadSubjectsBtn.disabled = true;
        loadBtnText.classList.add('d-none');
        loadBtnSpinner.classList.remove('d-none');
        subjectsContainer.classList.remove('d-none'); // Show the subjects container

        // Fetch subjects from the server
        fetch(`/curriculum/${curriculumId}/fetch`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('Failed to load subjects');
            }
            return res.json();
        })
        .then(data => {
            if (!Array.isArray(data)) {
                console.error('Invalid response format:', data);
                throw new Error('Invalid response format');
            }
            if (!data.length) {
                yearTabs.innerHTML = '';
                subjectsTableBody.innerHTML = '<div class="text-muted text-center">No subjects found.</div>';
                return;
            }

            const grouped = {};
            data.forEach(subj => {
                const key = `year${subj.year_level}`;
                if (!grouped[key]) grouped[key] = { '1st': [], '2nd': [] };
                if (grouped[key][subj.semester]) {
                    grouped[key][subj.semester].push(subj);
                }
            });

            let tabIndex = 0;
            for (const [key, semesters] of Object.entries(grouped)) {
                const year = key.replace('year', '');
                const isActive = tabIndex === 0 ? 'active' : '';

                yearTabs.insertAdjacentHTML('beforeend', `
                    <li class="nav-item">
                        <button class="nav-link ${isActive}" style="color: #198754; font-weight: 500;" data-bs-toggle="tab" data-bs-target="#tab-${key}" type="button" role="tab">Year ${year}</button>
                    </li>
                `);

                const semesterTables = Object.entries(semesters).map(([semester, subjects]) => {
                    if (!subjects.length) return '';

                    const rows = subjects.map(s => `
                        <tr>
                            <td><input type="checkbox" class="form-check-input subject-checkbox" name="subject_ids[]" value="${s.id}" data-year="${s.year_level}" data-semester="${s.semester}"></td>
                            <td>${s.subject_code}</td>
                            <td>${s.subject_description}</td>
                            <td>${s.year_level}</td>
                            <td>${s.semester}</td>
                        </tr>
                    `).join('');

                    return `
                        <h5 class="mt-4 text-success">${semester} Semester</h5>
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th></th>
                                    <th>Subject Code</th>
                                    <th>Description</th>
                                    <th>Year</th>
                                    <th>Semester</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows}
                            </tbody>
                        </table>
                    `;
                }).join('');

                subjectsTableBody.insertAdjacentHTML('beforeend', `
                    <div class="tab-pane fade ${isActive ? 'show active' : ''}" id="tab-${key}" role="tabpanel">
                        ${semesterTables}
                    </div>
                `);

                tabIndex++;
            }

            subjectsContainer.classList.remove('d-none');
        })
        .catch(() => {
            subjectsTableBody.innerHTML = '<div class="text-danger text-center">Failed to load subjects.</div>';
        })
        .finally(() => {
            loadSubjectsBtn.disabled = false;
            loadBtnText.classList.remove('d-none');
            loadBtnSpinner.classList.add('d-none');
        });
    });

    // Select/Unselect All Handler
    document.addEventListener('click', function (e) {
        if (e.target.closest('#selectAllBtn')) {
            const btn = e.target.closest('#selectAllBtn');
            let allSelected = btn.dataset.selected === 'true';
            allSelected = !allSelected;
            btn.dataset.selected = allSelected;
            document.querySelectorAll('.subject-checkbox').forEach(cb => cb.checked = allSelected);
            btn.classList.toggle('btn-outline-success', !allSelected);
            btn.classList.toggle('btn-success', allSelected);
            btn.innerHTML = allSelected
                ? '<i class="bi bi-x-square me-1"></i> Unselect All'
                : '<i class="bi bi-check2-square me-1"></i> Select All';
        }
    });

    // Confirm Modal Submission
    document.getElementById('submitConfirmBtn')?.addEventListener('click', function () {
        document.getElementById('confirmForm')?.submit();
    });
});
</script>
@endpush
