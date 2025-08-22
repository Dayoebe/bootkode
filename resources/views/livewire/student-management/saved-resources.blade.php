<div>
    <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-bookmark mr-2 text-orange-500"></i>
                My Saved Resources
            </h2>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <div class="relative flex-grow">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search saved resources..." 
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    >
                    <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                </div>
                
                <button 
                    wire:click="$toggle('groupByCourse')"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 border border-gray-600 rounded-lg text-white transition-colors"
                    title="{{ $groupByCourse ? 'Ungroup by course' : 'Group by course' }}"
                >
                    <i class="fas {{ $groupByCourse ? 'fa-list' : 'fa-layer-group' }} mr-1"></i>
                    {{ $groupByCourse ? 'List View' : 'Group View' }}
                </button>
            </div>
        </div>
        
        <div class="mt-4 flex flex-wrap gap-3">
            <select 
                wire:model.live="filter"
                class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
            >
                <option value="all">All Types</option>
                <option value="lesson">Lessons</option>
                <option value="note">Notes</option>
                <option value="pdf">PDFs</option>
                <option value="video">Videos</option>
            </select>
            
            <select 
                wire:model.live="sort"
                class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
            >
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="course">By Course</option>
            </select>
        </div>
    </div>

    @if($groupByCourse)
        <!-- Grouped by Course View -->
        @forelse($groupedResources as $courseId => $resources)
            <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <img 
                            src="{{ $courses[$courseId]->thumbnail ?? asset('images/default-course.png') }}" 
                            alt="{{ $courses[$courseId]->title }}" 
                            class="w-10 h-10 rounded-lg object-cover mr-3"
                        >
                        {{ $courses[$courseId]->title }}
                        <span class="ml-2 text-sm text-gray-400">({{ $resources->count() }} items)</span>
                    </h3>
                    <span class="text-sm text-gray-400">
                        Instructor: {{ $courses[$courseId]->instructor->name }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($resources as $bookmark)
                        <div class="bg-gray-700 rounded-lg border border-gray-600 p-4 hover:border-orange-500 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-1 bg-gray-600 text-xs rounded-full text-white">
                                    {{ ucfirst($bookmark->type) }}
                                </span>
                                <button 
                                    wire:click="removeBookmark('{{ $bookmark->id }}')"
                                    class="text-gray-400 hover:text-orange-500 transition-colors"
                                    title="Remove bookmark"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <h4 class="font-bold text-white mb-1">
                                {{ $bookmark->resourceable->title ?? 'Untitled Resource' }}
                            </h4>
                            
                            @if($bookmark->resourceable->description ?? false)
                                <p class="text-gray-400 text-sm mb-3 line-clamp-2">
                                    {{ $bookmark->resourceable->description }}
                                </p>
                            @endif
                            
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>
                                    Saved {{ $bookmark->created_at->diffForHumans() }}
                                </span>
                                <a 
                                    href="{{ route('student.enrolled-courses') }}?highlight={{ $bookmark->resourceable_id }}"
                                    class="text-orange-500 hover:text-orange-400 transition-colors"
                                >
                                    View Resource <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-8 text-center">
                <i class="fas fa-bookmark text-5xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Saved Resources Yet</h3>
                <p class="text-gray-400 mb-4">
                    You haven't saved any resources yet. When you find useful lessons or materials, 
                    click the bookmark icon to save them here for easy access.
                </p>
                <a 
                    href="{{ route('student.course-catalog') }}" 
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 rounded-lg text-white transition-colors"
                >
                    <i class="fas fa-book-open mr-2"></i> Browse Courses
                </a>
            </div>
        @endforelse
    @else
        <!-- Default List View -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($savedResources as $bookmark)
                <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-5 hover:border-orange-500 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                        <span class="px-2 py-1 bg-gray-700 text-xs rounded-full text-white">
                            {{ ucfirst($bookmark->type) }}
                        </span>
                        <button 
                            wire:click="removeBookmark('{{ $bookmark->id }}')"
                            class="text-gray-400 hover:text-orange-500 transition-colors"
                            title="Remove bookmark"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="flex items-center mb-3">
                        @if($bookmark->course)
                            <img 
                                src="{{ $bookmark->course->thumbnail ?? asset('images/default-course.png') }}" 
                                alt="{{ $bookmark->course->title }}" 
                                class="w-10 h-10 rounded-lg object-cover mr-3"
                            >
                            <div>
                                <h4 class="text-sm font-medium text-gray-400">
                                    {{ $bookmark->course->title }}
                                </h4>
                                <h3 class="font-bold text-white">
                                    {{ $bookmark->resourceable->title ?? 'Untitled Resource' }}
                                </h3>
                            </div>
                        @else
                            <h3 class="font-bold text-white">
                                {{ $bookmark->resourceable->title ?? 'Untitled Resource' }}
                            </h3>
                        @endif
                    </div>
                    
                    @if($bookmark->resourceable->description ?? false)
                        <p class="text-gray-400 text-sm mb-4 line-clamp-3">
                            {{ $bookmark->resourceable->description }}
                        </p>
                    @endif
                    
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>
                            Saved {{ $bookmark->created_at->diffForHumans() }}
                        </span>
                        <a 
                            href="{{ route('student.enrolled-courses') }}?highlight={{ $bookmark->resourceable_id }}"
                            class="text-orange-500 hover:text-orange-400 transition-colors flex items-center"
                        >
                            Open <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-8 text-center">
                    <i class="fas fa-bookmark text-5xl text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-bold text-white mb-2">No Saved Resources Yet</h3>
                    <p class="text-gray-400 mb-4">
                        You haven't saved any resources yet. When you find useful lessons or materials, 
                        click the bookmark icon to save them here for easy access.
                    </p>
                    <a 
                        href="{{ route('student.course-catalog') }}" 
                        class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 rounded-lg text-white transition-colors"
                    >
                        <i class="fas fa-book-open mr-2"></i> Browse Courses
                    </a>
                </div>
            @endforelse
        </div>
        

      


        @if($savedResources->hasPages())
            <div class="mt-6">
                {{ $savedResources->links() }}
            </div>
        @endif
    @endif
</div>