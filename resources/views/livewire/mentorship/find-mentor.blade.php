<div class="min-h-screen bg-themed-primary transition-colors duration-300">
    <!-- Header -->
    <div class="bg-themed-secondary shadow-sm border-b border-themed-primary transition-colors duration-300">
        <div class="px-6 py-8">
            <h1 class=" font-bold text-themed-primary mb-2 transition-colors duration-300 bk-page-title">Find Your Perfect Mentor</h1>
            <p class="text-xl text-themed-secondary transition-colors duration-300">Discover expert mentors in your field</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="w-full px-6 pt-4">
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg animate-fade-in">
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="w-full px-6 pt-4">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg animate-fade-in">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="px-6 py-8">
        <!-- Search and Filters -->
        <div class="bg-themed-secondary rounded-lg shadow-sm p-6 mb-8 border border-themed-primary transition-colors duration-300">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
                <h2 class="text-2xl font-bold text-themed-primary mb-4 lg:mb-0 transition-colors duration-300">Browse Mentors</h2>
                <div class="text-sm text-themed-secondary transition-colors duration-300">
                    <span class="bg-brand/10 text-brand px-3 py-1 rounded-full border border-brand/30">
                        {{ $mentors->total() }} mentors available
                    </span>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <input aria-label="Search by name, skills, or specialization" wire:model.live.debounce.300ms="searchTerm" type="text"
                        placeholder="Search by name, skills, or specialization..."
                        class="w-full pl-12 pr-4 py-4 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent text-lg bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300">
                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                        <i aria-hidden="true" class="fas fa-search text-themed-tertiary transition-colors duration-300"></i>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <select aria-label="Experience filter" wire:model.live="experienceFilter"
                    class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                    <option value="">All Experience</option>
                    <option value="junior">Junior Level</option>
                    <option value="mid">Mid Level</option>
                    <option value="senior">Senior Level</option>
                    <option value="expert">Expert Level</option>
                </select>

                <select aria-label="Availability filter" wire:model.live="availabilityFilter"
                    class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                    <option value="available">Available Now</option>
                    <option value="all">All Mentors</option>
                </select>

                <select aria-label="Rating filter" wire:model.live="ratingFilter"
                    class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                    <option value="">Any Rating</option>
                    <option value="4.5">4.5+ Stars</option>
                    <option value="4">4+ Stars</option>
                    <option value="3.5">3.5+ Stars</option>
                </select>

                <select aria-label="Price range filter" wire:model.live="priceRangeFilter"
                    class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                    <option value="">Any Price</option>
                    <option value="0-0">Free</option>
                    <option value="0-50">$0-50/hr</option>
                    <option value="50-100">$50-100/hr</option>
                    <option value="100-1000">$100+/hr</option>
                </select>

                <select aria-label="Specialization filter" wire:model.live="specializationFilter"
                    class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                    <option value="">All Specializations</option>
                    <option value="Web Development">Web Development</option>
                    <option value="Mobile Development">Mobile Development</option>
                    <option value="Data Science">Data Science</option>
                    <option value="DevOps">DevOps</option>
                    <option value="UI/UX Design">UI/UX Design</option>
                </select>

                <div class="flex items-center space-x-2">
                    <button wire:click="$set('viewMode', 'grid')"
                        class="p-2 rounded-lg transition-colors duration-300 {{ $viewMode === 'grid' ? 'bg-brand/20 text-brand' : 'text-themed-tertiary hover:text-themed-secondary' }}">
                        <i aria-hidden="true" class="fas fa-th"></i>
                    </button>
                    <button wire:click="$set('viewMode', 'list')"
                        class="p-2 rounded-lg transition-colors duration-300 {{ $viewMode === 'list' ? 'bg-brand/20 text-brand' : 'text-themed-tertiary hover:text-themed-secondary' }}">
                        <i aria-hidden="true" class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mentors Grid/List -->
        @if($mentors->count() > 0)
            @if($viewMode === 'grid')
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($mentors as $mentor)
                        <div class="bg-themed-secondary rounded-lg shadow-sm  transition-all duration-300 overflow-hidden border border-themed-primary hover:border-brand">
                            <!-- Mentor Card -->
                            <div class="p-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-16 h-16 bg-brand rounded-full flex items-center justify-center text-white dark:text-brand-ink text-2xl font-bold">
                                        {{ substr($mentor->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">{{ $mentor->user->name }}</h3>
                                        <p class="text-sm text-themed-secondary transition-colors duration-300">{{ $mentor->experience_label }}</p>
                                        <div class="flex items-center mt-1">
                                            <div class="text-yellow-400 mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i aria-hidden="true" class="fas fa-star {{ $i <= floor($mentor->rating) ? '' : 'text-themed-tertiary' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-themed-secondary transition-colors duration-300">({{ $mentor->total_reviews }})</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Availability Status -->
                                <div class="mb-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors duration-300 {{ $mentor->canAcceptMentees() ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                        <div class="w-2 h-2 rounded-full {{ $mentor->canAcceptMentees() ? 'bg-green-400' : 'bg-red-400' }} mr-2"></div>
                                        {{ $mentor->availability_status }}
                                    </span>
                                </div>

                                <!-- Bio -->
                                <p class="text-themed-secondary text-sm mb-4 line-clamp-3 transition-colors duration-300">
                                    {{ Str::limit($mentor->bio, 120) }}
                                </p>

                                <!-- Specializations -->
                                <div class="mb-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($mentor->specializations ?? [], 0, 3) as $spec)
                                            <span class="bg-brand/10 text-brand text-xs px-2 py-1 rounded-full border border-brand/30 transition-colors duration-300">
                                                {{ $spec }}
                                            </span>
                                        @endforeach
                                        @if(count($mentor->specializations ?? []) > 3)
                                            <span class="bg-themed-tertiary text-themed-secondary text-xs px-2 py-1 rounded-full border border-themed-primary transition-colors duration-300">
                                                +{{ count($mentor->specializations) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Pricing -->
                                <div class="mb-4">
                                    @if($mentor->offers_free_sessions)
                                        <span class="text-green-600 dark:text-green-400 font-semibold">Free Sessions Available</span>
                                    @else
                                        <span class="text-themed-primary font-semibold transition-colors duration-300">₦{{ number_format($mentor->hourly_rate, 0) }}/hour</span>
                                    @endif
                                </div>

                                <!-- Stats -->
                                <div class="flex justify-between text-sm text-themed-secondary mb-6 transition-colors duration-300">
                                    <span><i aria-hidden="true" class="fas fa-users mr-1"></i>{{ $mentor->total_mentees }} mentees</span>
                                    <span><i aria-hidden="true" class="fas fa-calendar mr-1"></i>{{ $mentor->total_sessions }} sessions</span>
                                    <span><i aria-hidden="true" class="fas fa-clock mr-1"></i>{{ $mentor->years_experience }}+ years</span>
                                </div>

                                <!-- Actions -->
                                <div class="space-y-2">
                                    <button wire:click="selectMentor({{ $mentor->id }})"
                                        class="w-full bk-button bk-button-primary   px-4 py-3 rounded-lg transition-colors font-semibold">
                                        <i aria-hidden="true" class="fas fa-user mr-2"></i>View Profile
                                    </button>
                                    @if($mentor->canAcceptMentees())
                                        <button wire:click="requestMentorship({{ $mentor->id }})"
                                            class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                                            <i aria-hidden="true" class="fas fa-handshake mr-2"></i>Request Mentorship
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- List View -->
                <div class="space-y-4">
                    @foreach($mentors as $mentor)
                        <div class="bg-themed-secondary rounded-lg shadow-sm hover:shadow-sm transition-all duration-300 p-6 border border-themed-primary hover:border-brand">
                            <div class="flex flex-col lg:flex-row lg:items-center">
                                <!-- Mentor Info -->
                                <div class="flex items-center space-x-4 mb-4 lg:mb-0 lg:flex-1">
                                    <div class="w-12 h-12 bg-brand rounded-full flex items-center justify-center text-white dark:text-brand-ink font-bold">
                                        {{ substr($mentor->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-themed-primary transition-colors duration-300">{{ $mentor->user->name }}</h3>
                                        <p class="text-sm text-themed-secondary transition-colors duration-300">{{ $mentor->experience_label }} • {{ $mentor->years_experience }}+ years</p>
                                        <div class="flex items-center mt-1">
                                            <div class="text-yellow-400 mr-2 text-sm">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i aria-hidden="true" class="fas fa-star {{ $i <= floor($mentor->rating) ? '' : 'text-themed-tertiary' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-themed-secondary transition-colors duration-300">({{ $mentor->total_reviews }})</span>
                                            <span class="mx-2 text-themed-tertiary transition-colors duration-300">•</span>
                                            <span class="text-sm {{ $mentor->canAcceptMentees() ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} transition-colors duration-300">
                                                {{ $mentor->availability_status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Specializations -->
                                <div class="lg:flex-1 lg:mx-6 mb-4 lg:mb-0">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($mentor->specializations ?? [], 0, 4) as $spec)
                                            <span class="bg-brand/10 text-brand text-xs px-2 py-1 rounded-full border border-brand/30 transition-colors duration-300">
                                                {{ $spec }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Pricing and Actions -->
                                <div class="lg:flex-shrink-0">
                                    <div class="text-center lg:text-right mb-4">
                                        @if($mentor->offers_free_sessions)
                                            <div class="text-green-600 dark:text-green-400 font-semibold">Free Sessions</div>
                                        @else
                                            <div class="text-themed-primary font-semibold text-lg transition-colors duration-300">₦{{ number_format($mentor->hourly_rate, 0) }}/hr</div>
                                        @endif
                                        <div class="text-sm text-themed-secondary transition-colors duration-300">{{ $mentor->total_mentees }} mentees</div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button wire:click="selectMentor({{ $mentor->id }})"
                                            class="bk-button bk-button-primary   px-4 py-2 rounded-lg transition-colors text-sm">
                                            <i aria-hidden="true" class="fas fa-user mr-1"></i>View
                                        </button>
                                        @if($mentor->canAcceptMentees())
                                            <button wire:click="requestMentorship({{ $mentor->id }})"
                                                class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                                <i aria-hidden="true" class="fas fa-handshake mr-1"></i>Request
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-8">
                {{ $mentors->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-themed-secondary rounded-lg shadow-sm border border-themed-primary transition-colors duration-300">
                <div class="text-6xl text-themed-tertiary mb-4 transition-colors duration-300">
                    <i aria-hidden="true" class="fas fa-search"></i>
                </div>
                <h3 class="text-xl font-semibold text-themed-primary mb-2 transition-colors duration-300">No mentors found</h3>
                <p class="text-themed-secondary mb-6 transition-colors duration-300">Try adjusting your search criteria or filters</p>
                <button wire:click="$set('searchTerm', '')" wire:click="$set('experienceFilter', '')" wire:click="$set('ratingFilter', '')"
                    class="bk-button bk-button-primary   px-6 py-3 rounded-lg transition-colors">
                    <i aria-hidden="true" class="fas fa-redo mr-2"></i>Clear Filters
                </button>
            </div>
        @endif
    </div>

    <!-- Mentor Profile Modal -->
    @if($showMentorModal && $selectedMentor)
        <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-75 z-50 flex items-center justify-center p-4" x-data="{ dialogOpen: true }" x-trap.inert.noscroll="dialogOpen" x-on:keydown.escape.window="dialogOpen = false; $wire.closeModal()" role="dialog" aria-modal="true" aria-label="Find Mentor dialog" tabindex="-1">
            <div class="bg-themed-secondary rounded-lg shadow-sm max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-themed-primary transition-colors duration-300">
                <div class="p-6 border-b border-themed-primary transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-themed-primary transition-colors duration-300">Mentor Profile</h3>
                        <button aria-label="Close dialog" wire:click="closeModal" class="text-themed-secondary hover:text-themed-primary transition-colors">
                            <i aria-hidden="true" class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Profile Header -->
                    <div class="flex items-center space-x-6 mb-6">
                        <div class="w-24 h-24 bg-brand rounded-full flex items-center justify-center text-white dark:text-brand-ink text-4xl font-bold">
                            {{ substr($selectedMentor->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-2xl font-bold text-themed-primary transition-colors duration-300">{{ $selectedMentor->user->name }}</h4>
                            <p class="text-lg text-themed-secondary transition-colors duration-300">{{ $selectedMentor->experience_label }} • {{ $selectedMentor->years_experience }}+ years experience</p>
                            <div class="flex items-center mt-2">
                                <div class="text-yellow-400 mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i aria-hidden="true" class="fas fa-star {{ $i <= floor($selectedMentor->rating) ? '' : 'text-themed-tertiary' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-sm text-themed-secondary transition-colors duration-300">({{ $selectedMentor->total_reviews }} reviews)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bio -->
                    <div class="mb-6">
                        <h5 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">About</h5>
                        <p class="text-themed-secondary leading-relaxed transition-colors duration-300">{{ $selectedMentor->bio }}</p>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h5 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Specializations</h5>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedMentor->specializations ?? [] as $spec)
                                    <span class="bg-brand/10 text-brand px-3 py-1 rounded-full text-sm border border-brand/30 transition-colors duration-300">{{ $spec }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h5 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Skills</h5>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedMentor->skills ?? [] as $skill)
                                    <span class="bg-green-100/50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-3 py-1 rounded-full text-sm border border-green-200/50 dark:border-green-800 transition-colors duration-300">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if($selectedMentor->mentoring_approach)
                        <div class="mb-6">
                            <h5 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Mentoring Approach</h5>
                            <p class="text-themed-secondary transition-colors duration-300">{{ $selectedMentor->mentoring_approach }}</p>
                        </div>
                    @endif

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-6 p-4 bg-themed-tertiary rounded-lg transition-colors duration-300">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-themed-primary transition-colors duration-300">{{ $selectedMentor->total_mentees }}</div>
                            <div class="text-sm text-themed-secondary transition-colors duration-300">Total Mentees</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-themed-primary transition-colors duration-300">{{ $selectedMentor->total_sessions }}</div>
                            <div class="text-sm text-themed-secondary transition-colors duration-300">Sessions</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-themed-primary transition-colors duration-300">
                                @if($selectedMentor->offers_free_sessions)
                                    Free
                                @else
                                    ₦{{ number_format($selectedMentor->hourly_rate, 0) }}
                                @endif
                            </div>
                            <div class="text-sm text-themed-secondary transition-colors duration-300">Per Hour</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4">
                        <button wire:click="closeModal"
                            class="flex-1 px-6 py-3 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors transition-colors duration-300">
                            Close
                        </button>
                        @if($selectedMentor->canAcceptMentees())
                            <button wire:click="requestMentorship({{ $selectedMentor->id }})"
                                class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded-lg transition-colors">
                                <i aria-hidden="true" class="fas fa-handshake mr-2"></i>Request Mentorship
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Request Mentorship Modal -->
    @if($showRequestModal && $selectedMentor)
        <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-75 z-50 flex items-center justify-center p-4" x-data="{ dialogOpen: true }" x-trap.inert.noscroll="dialogOpen" x-on:keydown.escape.window="dialogOpen = false; $wire.closeModal()" role="dialog" aria-modal="true" aria-label="Find Mentor dialog" tabindex="-1">
            <div class="bg-themed-secondary rounded-lg shadow-sm max-w-3xl w-full max-h-[90vh] overflow-y-auto border border-themed-primary transition-colors duration-300">
                <div class="p-6 border-b border-themed-primary transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-themed-primary transition-colors duration-300">Request Mentorship</h3>
                        <button aria-label="Close dialog" wire:click="closeModal" class="text-themed-secondary hover:text-themed-primary transition-colors">
                            <i aria-hidden="true" class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <form wire:submit="submitMentorshipRequest" class="p-6 space-y-6">
                    <div class="bg-brand/10 border border-brand/30 rounded-lg p-4 transition-colors duration-300">
                        <p class="text-sm text-themed-primary transition-colors duration-300">
                            <strong>Requesting mentorship from:</strong> {{ $selectedMentor->user->name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Introduction Message *</label>
                        <textarea aria-label="Tell the mentor about yourself, why you're interested in their mentorship, and what you hope to achieve" wire:model="requestMessage" rows="5"
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                            placeholder="Tell the mentor about yourself, why you're interested in their mentorship, and what you hope to achieve..."></textarea>
                        @error('requestMessage') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Your Goals *</label>
                        @foreach($goals as $index => $goal)
                            <div class="flex items-center space-x-2 mb-2">
                                <input aria-label="e.g., Learn advanced React patterns" type="text" wire:model="goals.{{ $index }}"
                                    class="flex-1 px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                    placeholder="e.g., Learn advanced React patterns">
                                @if(count($goals) > 1)
                                    <button type="button" wire:click="removeGoal({{ $index }})"
                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300" aria-label="Close">
                                        <i aria-hidden="true" class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                        <button type="button" wire:click="addGoal"
                            class="text-brand hover:text-brand text-sm transition-colors">
                            <i aria-hidden="true" class="fas fa-plus mr-1"></i>Add Goal
                        </button>
                        @error('goals') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Your Expectations *</label>
                        @foreach($expectations as $index => $expectation)
                            <div class="flex items-center space-x-2 mb-2">
                                <input aria-label="e.g., Weekly code reviews" type="text" wire:model="expectations.{{ $index }}"
                                    class="flex-1 px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                    placeholder="e.g., Weekly code reviews">
                                @if(count($expectations) > 1)
                                    <button type="button" wire:click="removeExpectation({{ $index }})"
                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300" aria-label="Close">
                                        <i aria-hidden="true" class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                        <button type="button" wire:click="addExpectation"
                            class="text-brand hover:text-brand text-sm transition-colors">
                            <i aria-hidden="true" class="fas fa-plus mr-1"></i>Add Expectation
                        </button>
                        @error('expectations') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Duration (weeks) *</label>
                        <input aria-label="Duration weeks" type="number" wire:model="durationWeeks" min="4" max="52"
                            class="w-full px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                        @error('durationWeeks') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-themed-primary transition-colors duration-300">
                        <button type="button" wire:click="closeModal"
                            class="px-6 py-3 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors duration-300">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bk-button bk-button-primary   rounded-lg transition-colors">
                            <i aria-hidden="true" class="fas fa-paper-plane mr-2"></i>Send Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 dark:bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-themed-secondary rounded-lg p-6 shadow-sm border border-themed-primary transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-6 w-6 text-brand" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-themed-primary font-medium transition-colors duration-300">Loading...</span>
            </div>
        </div>
    </div>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>
