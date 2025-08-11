<div>
    @section('dashboard-title', 'Super Admin Dashboard')
 <!-- Welcome Header -->
 <div class="bg-gray-800 p-8 rounded-xl shadow-lg border border-gray-700 flex flex-col md:flex-row items-center justify-between">
    <div class="text-white">
        <h1 class="text-3xl font-extrabold mb-2">Welcome Back, {{ auth()->user()->name }}!</h1>
        {{-- <p class="text-gray-400 text-lg">You have {{ $inProgressCourses->count() }} courses in progress.</p> --}}
    </div>
    <div class="mt-4 md:mt-0">
        <span class="text-6xl animate-pulse">👨‍🎓</span>
    </div>
</div>
    <!-- Main Content Area -->
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <!-- Dynamically load components based on the current route name -->
        @if (request()->routeIs('super_admin.dashboard'))
            <!-- Default dashboard content -->
            <div class="bg-white rounded-lg shadow p-6">
                <h1 class="text-2xl font-bold text-gray-800">Super Admin Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome back! Select an option from the sidebar.</p>
            </div>
        @elseif(request()->routeIs('profile.view'))
            <livewire:profile-management />
        @elseif(request()->routeIs('all-course'))
            <livewire:component.course-management.all-courses />
        @elseif(request()->routeIs('course_management.create_course'))
            <livewire:component.course-management.create-course />
        @elseif(request()->routeIs('course_management.course_categories'))
            <livewire:component.course-management.course-categories />
        @elseif(request()->routeIs('course_management.course_builder'))
            <livewire:component.course-management.course-builder />
        @elseif(request()->routeIs('course_management.course_reviews'))
            <livewire:component.course-management.course-reviews />
        @elseif(request()->routeIs('course_management.course_approvals'))
            <livewire:component.user-management />
            @elseif(request()->routeIs('user-management'))
            <livewire:component.user-management />
        @else
            <!-- Fallback content -->
            <div class="bg-white rounded-lg shadow p-6">
                <h1 class="text-2xl font-bold text-gray-800">Page Not Found</h1>
                <p class="text-gray-600 mt-2">The requested page could not be found.</p>
            </div>
        @endif
    </div>


</div>
