<div class="space-y-6">
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg animate__animated animate__fadeIn">
            {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Breadcrumb -->
    <div class="flex items-center text-sm text-gray-400 mb-4">
        <span>Assessment Manager</span>
        @if ($activeView !== 'list')
            <i class="fas fa-chevron-right mx-2"></i>
            @if ($activeView === 'create')
                <span class="text-blue-400">Create Assessment</span>
            @elseif ($activeView === 'edit')
                <span class="text-yellow-400">Edit Assessment</span>
            @elseif ($activeView === 'questions')
                <span class="text-purple-400">Manage Questions</span>
            @endif
        @endif
    </div>

    <!-- Main Content Area -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-900 px-6 py-4 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if ($activeView === 'list')
                        <h3 class="text-lg font-medium text-white">Lesson Assessments</h3>
                        <span class="px-2 py-1 bg-gray-700 text-gray-300 text-sm rounded-full">
                            {{ count($assessments) }} assessments
                        </span>
                    @else
                        <button wire:click="backToList" 
                                class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                        </button>
                        <h3 class="text-lg font-medium text-white">
                            @if ($activeView === 'create')
                                Create New Assessment
                            @elseif ($activeView === 'edit')
                                Edit Assessment
                            @elseif ($activeView === 'questions')
                                Manage Questions
                            @endif
                        </h3>
                    @endif
                </div>
                
                @if ($activeView === 'list')
                    <button wire:click="toggleCreateForm"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Assessment
                    </button>
                @endif
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-6">
            @if ($activeView === 'list')
                @include('livewire.course-management.course-builder.assessment.list')
            @elseif ($activeView === 'create' || $activeView === 'edit')
                @include('livewire.course-management.course-builder.assessment.form')
            @elseif ($activeView === 'questions')
                @include('livewire.course-management.course-builder.assessment.questions')
            @endif
        </div>
    </div>

    <!-- Assessment Tips -->
    <div class="bg-blue-900/30 border border-blue-700 rounded-lg p-4">
        <h4 class="text-blue-300 font-medium mb-2">
            <i class="fas fa-info-circle mr-2"></i>Assessment Guidelines
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-blue-200 text-sm">
            <div>
                <p class="font-medium mb-1">Quiz Assessments</p>
                <ul class="space-y-1 text-xs">
                    <li>• Best for knowledge retention and concept checks</li>
                    <li>• Support multiple question types (MCQ, T/F, Fill-in)</li>
                    <li>• Automatic grading and instant feedback</li>
                </ul>
            </div>
            <div>
                <p class="font-medium mb-1">Project Assessments</p>
                <ul class="space-y-1 text-xs">
                    <li>• Ideal for hands-on skill application</li>
                    <li>• File uploads and peer review options</li>
                    <li>• Rubric-based evaluation</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .animate__fadeIn {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .sortable-item {
        transition: transform 0.2s ease;
    }
    
    .sortable-item:hover {
        transform: translateY(-2px);
    }
</style>
@endpush