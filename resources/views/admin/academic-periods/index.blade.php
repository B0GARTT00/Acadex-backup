@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Manage Academic Periods</h1>

    <button type="button" 
            class="mb-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded"
            data-bs-toggle="modal" 
            data-bs-target="#addAcademicPeriodModal">
        Add Academic Period
    </button>

    {{-- Periods Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left border">Academic Year</th>
                    <th class="p-3 text-left border">Semester</th>
                    <th class="p-3 text-center border">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($periods as $period)
                    <tr class="hover:bg-gray-100">
                        <td class="p-3 border">{{ $period->academic_year }}</td>
                        <td class="p-3 border">{{ ucfirst($period->semester) }}</td>
                        <td class="p-3 border text-center">{{ $period->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Academic Period Modal -->
<div class="modal fade" id="addAcademicPeriodModal" tabindex="-1" aria-labelledby="addAcademicPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAcademicPeriodModalLabel">Add New Academic Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/academic-periods" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                        <input type="text" name="academic_year" id="academic_year" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                    </div>
                    <div class="mb-4">
                        <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                        <select name="semester" id="semester" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                            <option value="">Select Semester</option>
                            <option value="first">First Semester</option>
                            <option value="second">Second Semester</option>
                            <option value="summer">Summer Term</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="is_active" class="block text-sm font-medium text-gray-700">Is Active</label>
                        <div class="mt-1">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   class="form-checkbox h-5 w-5 text-indigo-600 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Academic Period</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection