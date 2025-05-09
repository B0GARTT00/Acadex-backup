@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5" style="max-height: calc(100vh - 120px); overflow-y: auto;">
    <h1 class="text-2xl font-bold mb-4">
        <i class="bi bi-book-half text-success me-2"></i>
        Import GE Subjects from Curriculum
    </h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Curriculum Dropdown --}}
    <div class="mb-4">
        <label for="curriculumSelect" class="form-label fw-semibold">Select Curriculum</label>
        <select id="curriculumSelect" class="form-select shadow-sm">
            <option value="">-- Choose Curriculum --</option>
            @foreach($curriculums as $curriculum)
                <option value="{{ $curriculum->id }}">
                    {{ $curriculum->name }} - {{ $curriculum->course->course_code ?? 'N/A' }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Load Button --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <button id="loadSubjectsBtn" class="btn btn-success d-none">
            <span id="loadBtnText"><i class="bi bi-arrow-repeat me-1"></i> Load GE Subjects</span>
            <span id="loadBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    </div>

    {{-- Subject Selection Form --}}
    <form method="POST" action="{{ route('ge-coordinator.subjects.confirm') }}" id="confirmForm">
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
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Import</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to import the selected GE subjects?
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submitConfirmBtn" class="btn btn-success">Yes, Import</button>
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
    const submitConfirmBtn = document.getElementById('submitConfirmBtn');
    const confirmForm = document.getElementById('confirmForm');

    // Toggle load button based on curriculum selection
    curriculumSelect.addEventListener('change', function () {
        loadSubjectsBtn.classList.toggle('d-none', !this.value);
        subjectsContainer.classList.add('d-none');
        yearTabs.innerHTML = '';
        subjectsTableBody.innerHTML = '';
    });

    // Load subjects when button is clicked
    loadSubjectsBtn.addEventListener('click', function () {
        const curriculumId = curriculumSelect.value;
        if (!curriculumId) return;

        formCurriculumId.value = curriculumId;
        yearTabs.innerHTML = '';
        subjectsTableBody.innerHTML = '';
        loadSubjectsBtn.disabled = true;
        loadBtnText.classList.add('d-none');
        loadBtnSpinner.classList.remove('d-none');

        // Fetch subjects from the server
        fetch(`/ge-coordinator/subjects/${curriculumId}/fetch`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                yearTabs.innerHTML = '';
                subjectsTableBody.innerHTML = '<div class="text-muted text-center">No GE subjects found in this curriculum.</div>';
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

                // Add year tab
                yearTabs.insertAdjacentHTML('beforeend', `
                    <li class="nav-item">
                        <button class="nav-link ${isActive}" style="color: #198754; font-weight: 500;" 
                                data-bs-toggle="tab" data-bs-target="#tab-${key}" type="button" role="tab" 
                                data-year="${year}">
                            Year ${year}
                        </button>
                    </li>
                `);

                // Add semester tables
                const semesterTables = Object.entries(semesters).map(([semester, subjects]) => {
                    if (!subjects.length) return '';

                    const rows = subjects.map(s => `
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input subject-checkbox" type="checkbox" 
                                           value="${s.id}" 
                                           ${s.is_universal ? '' : 'disabled'}>
                                    <input type="hidden" name="curriculum_subject_id[]" value="${s.id}">
                                </div>
                            </td>
                            <td>${s.subject_code}</td>
                            <td>${s.subject_description}</td>
                            <td>${s.year_level}</td>
                            <td>${s.semester} Semester</td>
                            <td>${s.is_universal ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}</td>
                        </tr>
                    `).join('');

                    return `
                        <h5 class="mt-4 text-success">${semester} Semester</h5>
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th>Subject Code</th>
                            <th>GE Subject</th>
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

                // Add tab content
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

    // Handle select all/none
    document.getElementById('selectAllBtn').addEventListener('click', function() {
        const btn = this;
        const allSelected = btn.dataset.selected === 'true';
        
        // Get the current tab's table
        const currentTab = document.querySelector('.nav-link.active');
        const currentTabId = currentTab.getAttribute('data-bs-target').replace('#', '');
        const currentTable = document.querySelector(`#${currentTabId} .table`);
        
        // Toggle checkboxes in the current year level
        const checkboxes = currentTable.querySelectorAll('input[type="checkbox"]:not(:disabled)');
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allSelected;
        });
        
        // Update button state
        const isSelected = !allSelected;
        btn.dataset.selected = isSelected;
        btn.innerHTML = isSelected ? '<i class="bi bi-x-square me-1"></i> Deselect All' : '<i class="bi bi-check2-square me-1"></i> Select All';
    });

    // Handle confirmation modal submit
    submitConfirmBtn.addEventListener('click', function() {
        // Get all checked checkboxes
        const checkedCheckboxes = document.querySelectorAll('.subject-checkbox:checked');
        
        // Convert to array of values
        const selectedSubjects = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);
        
        if (selectedSubjects.length === 0) {
            alert('Please select at least one subject.');
            return;
        }

        // Clear any existing hidden inputs
        const existingInputs = confirmForm.querySelectorAll('input[name="subject_ids[]"]');
        existingInputs.forEach(input => input.remove());

        // Create new hidden input for each selected subject
        selectedSubjects.forEach(subjectId => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'subject_ids[]';
            hiddenInput.value = subjectId;
            confirmForm.appendChild(hiddenInput);
        });

        confirmForm.submit();
    });
});
</script>
@endpush
