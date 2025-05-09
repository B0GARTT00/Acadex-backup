@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Create Academic Period</h1>

    <form action="{{ route('admin.academicPeriods.store') }}" method="POST" class="max-w-md mx-auto">
        @csrf

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
                <option value="1st">First Semester</option>
                <option value="2nd">Second Semester</option>
                <option value="Summer">Summer Term</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="is_active" class="block text-sm font-medium text-gray-700">Is Active</label>
            <div class="mt-1">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       class="form-checkbox h-5 w-5 text-indigo-600 focus:ring-indigo-500">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Create Academic Period
            </button>
        </div>
    </form>
</div>
@endsection
