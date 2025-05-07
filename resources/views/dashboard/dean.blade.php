@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col" style="background-color: #BEE6C4; font-family: 'Segoe UI', sans-serif;">
  <div class="flex-1 flex flex-col">
    <!-- Main Content -->
    <main class="flex-1 p-6 overflow-auto">
      <h3 class="font-bold text-gray-800 text-base mb-6">Dashboard</h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Students Card -->
        <div class="bg-white p-6 rounded-lg border border-green-200 transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-md">
          <div class="flex items-center space-x-4">
            <div class="bg-green-600 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl">
              <i class="fas fa-user-graduate"></i>
            </div>
            <div>
              <div class="text-2xl font-bold text-green-800">{{ $studentCount }}</div>
              <div class="text-sm text-gray-600">Total Students</div>
            </div>
          </div>
        </div>

        <!-- Instructors Card -->
        <div class="bg-white p-6 rounded-lg border border-green-200 transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-md">
          <div class="flex items-center space-x-4">
            <div class="bg-green-600 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl">
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
              <div class="text-2xl font-bold text-green-800">{{ $instructorCount }}</div>
              <div class="text-sm text-gray-600">Total Instructors</div>
            </div>
          </div>
        </div>

        <!-- Courses Card -->
        <div class="bg-white p-6 rounded-lg border border-green-200 transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-md">
          <div class="flex items-center space-x-4">
            <div class="bg-green-600 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl">
              <i class="fas fa-book"></i>
            </div>
            <div>
              <div class="text-2xl font-bold text-green-800">{{ $courseCount }}</div>
              <div class="text-sm text-gray-600">Total Courses</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Cards Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Departments Card -->
        <div class="bg-white p-6 rounded-lg border border-green-200 transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-md">
          <div class="flex items-center space-x-4">
            <div class="bg-green-600 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl">
              <i class="fas fa-building"></i>
            </div>
            <div>
              <div class="text-2xl font-bold text-green-800">{{ $departmentCount }}</div>
              <div class="text-sm text-gray-600">Departments</div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white p-6 rounded-lg border border-green-200">
          <h4 class="font-semibold text-gray-800 mb-4">Recent Activity</h4>
          <div class="space-y-4">
            @forelse($recentActivities as $activity)
            <div class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <div class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center">
                  <i class="fas {{ $activity['icon'] }} text-sm"></i>
                </div>
              </div>
              <div>
                <p class="text-sm text-gray-800">{{ $activity['description'] }}</p>
                <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
              </div>
            </div>
            @empty
            <p class="text-sm text-gray-500">No recent activities</p>
            @endforelse
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<style>
  .sidebar-link {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    text-decoration: none;
    color: white;
    font-family: 'Segoe UI', sans-serif;
    transition: background-color 0.3s, font-weight 0.3s;
  }
  .sidebar-link:hover {
    background-color: #034532;
    font-weight: bold;
  }
  .sidebar-active {
    background-color: #034532 !important;
    font-weight: bold;
  }
</style>
@endsection
