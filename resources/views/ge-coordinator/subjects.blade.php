@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">📚 GE Subjects</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @if($subjects->isEmpty())
                <div class="text-center py-8">
                    <i class="fas fa-book fa-3x text-gray-300 mb-4"></i>
                    <p class="text-gray-600">No GE subjects found for the current academic period.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($subjects as $subject)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subject->subject_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subject->subject_description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $subject->credits }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('ge-coordinator.subjects.edit', $subject->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
