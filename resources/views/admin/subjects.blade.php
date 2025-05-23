@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Subjects</h1>

    <button type="button" 
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm transition-colors duration-200"
            data-bs-toggle="modal" 
            data-bs-target="#addSubjectModal">
        <i class="bi bi-plus-circle me-2"></i>
        Add Subject
    </button>

    <div class="mt-6">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            Code
                                            <button class="ml-2 p-1 rounded-full hover:bg-gray-100">
                                                <i class="bi bi-sort-down"></i>
                                            </button>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            Description
                                            <button class="ml-2 p-1 rounded-full hover:bg-gray-100">
                                                <i class="bi bi-sort-down"></i>
                                            </button>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            Units
                                            <button class="ml-2 p-1 rounded-full hover:bg-gray-100">
                                                <i class="bi bi-sort-down"></i>
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subjects as $subject)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $subject->subject_code }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $subject->subject_description ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $subject->units }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    @if($subjects->isEmpty())
    <div class="mt-6 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No subjects found</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new subject.</p>
        <div class="mt-6">
            <button type="button" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    data-bs-toggle="modal" 
                    data-bs-target="#addSubjectModal">
                <i class="bi bi-plus-circle me-2"></i>
                Add Subject
            </button>
        </div>
    </div>
    @endif
</div>
@endsection

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubjectModalLabel">Add New Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.createSubject') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="subject_code" class="block text-sm font-medium text-gray-700">Subject Code</label>
                        <input type="text" name="subject_code" id="subject_code" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                    </div>
                    <div class="mb-4">
                        <label for="subject_description" class="block text-sm font-medium text-gray-700">Subject Description</label>
                        <input type="text" name="subject_description" id="subject_description" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                    </div>
                    <div class="mb-4">
                        <label for="units" class="block text-sm font-medium text-gray-700">Units</label>
                        <input type="number" name="units" id="units" min="0" max="5"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
