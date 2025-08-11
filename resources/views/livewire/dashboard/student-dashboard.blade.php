<div>
    @section('dashboard-title', 'Student Dashboard')
    
    <!-- Welcome Header -->
    <div class="bg-gray-800 p-8 rounded-xl shadow-lg border border-gray-700 flex flex-col md:flex-row items-center justify-between">
        <div class="text-white">
            <h1 class="text-3xl font-extrabold mb-2">Welcome Back, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-400 text-lg">Your learning journey continues</p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="text-6xl animate-pulse">👨‍🎓</span>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        @if (request()->routeIs('student.dashboard'))
            <!-- Default student dashboard content -->
            <div class="grid grid-cols-1 gap-6">
                <livewire:component.student-management.enrolled-courses :compact="true" />
                {{-- <livewire:component.student-management.upcoming-assignments /> --}}
            </div>
            
        @elseif(request()->routeIs('student.enrolled-courses'))
            <livewire:component.student-management.enrolled-courses />
            
        @elseif(request()->routeIs('student.course-catalog'))
            <livewire:component.student-management.course-catalog />
            
        @elseif(request()->routeIs('student.learning-analytics'))
            <livewire:component.student-management.learning-analytics />
            
        @elseif(request()->routeIs('student.assignments'))
            <livewire:component.student-management.assignments />
            
        @else
            <!-- Fallback content -->
            <div class="bg-gray-800 rounded-lg shadow p-6 text-center">
                <h1 class="text-2xl font-bold text-white">Page Not Found</h1>
                <p class="text-gray-400 mt-2">The requested page could not be found.</p>
            </div>
        @endif
    </div>
</div>