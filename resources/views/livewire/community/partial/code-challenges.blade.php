<div>
    <!-- Code Challenges Header -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-orange-100 p-3 rounded-lg">
                    <i class="fas fa-trophy text-orange-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Code Challenges</h2>
                    <p class="text-gray-600 mt-1">Test your programming skills and compete with others</p>
                </div>
            </div>
            @if(auth()->user()->isInstructor() || auth()->user()->canManageUsers())
            <button wire:click="$set('showCreateForm', true)" 
                    class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>Create Challenge
            </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar with Leaderboard and My Submissions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- My Submissions -->
            @if($myChallenges->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">My Recent Submissions</h3>
                <div class="space-y-3">
                    @foreach($myChallenges as $submission)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h4 class="font-medium text-sm text-gray-900">{{ Str::limit($submission->challenge->title, 30) }}</h4>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="text-xs text-gray-500">{{ $submission->language }}</span>
                                <span class="text-xs bg-{{ $submission->status === 'passed' ? 'green' : ($submission->status === 'failed' ? 'red' : 'yellow') }}-100 
                                           text-{{ $submission->status === 'passed' ? 'green' : ($submission->status === 'failed' ? 'red' : 'yellow') }}-800 
                                           px-2 py-1 rounded-full">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-sm font-medium text-orange-600">{{ $submission->score }}pts</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Leaderboard -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-trophy text-orange-600 mr-2"></i>Leaderboard
                </h3>
                <div class="space-y-3">
                    @forelse($leaderboard as $index => $user)
                    <div class="flex items-center justify-between p-3 {{ $index < 3 ? 'bg-gradient-to-r from-yellow-50 to-orange-50' : 'bg-gray-50' }} rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-{{ $index === 0 ? 'yellow' : ($index === 1 ? 'gray' : 'orange') }}-500 
                                        text-white rounded-full flex items-center justify-center text-sm font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <h4 class="font-medium text-sm text-gray-900">{{ $user->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $user->submissions_count }} submissions</p>
                            </div>
                        </div>
                        <div class="text-sm font-bold text-orange-600">{{ $user->total_score }}pts</div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No submissions yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input type="text" wire:model.live="searchTerm" placeholder="Search challenges..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                    </div>
                    <div>
                        <select wire:model.live="difficultyFilter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                            <option value="all">All Difficulties</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="statusFilter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                            <option value="all">All Challenges</option>
                            <option value="not_attempted">Not Attempted</option>
                            <option value="attempted">Attempted</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button wire:click="$refresh" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Challenges Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($challenges as $challenge)
                <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-all duration-200 animate__animated animate__fadeIn">
                    <div class="p-6">
                        <!-- Challenge Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $challenge->title }}</h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $challenge->description }}</p>
                            </div>
                        </div>

                        <!-- Challenge Info -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <span class="bg-{{ $challenge->difficulty === 'easy' ? 'green' : ($challenge->difficulty === 'medium' ? 'yellow' : 'red') }}-100 
                                           text-{{ $challenge->difficulty === 'easy' ? 'green' : ($challenge->difficulty === 'medium' ? 'yellow' : 'red') }}-800 
                                           text-xs px-2 py-1 rounded-full font-medium">
                                    {{ ucfirst($challenge->difficulty) }}
                                </span>
                                <span class="text-sm text-orange-600 font-medium">{{ $challenge->points }} pts</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-code mr-1"></i>
                                {{ $challenge->submissions_count }} submissions
                            </div>
                        </div>

                        <!-- Tags -->
                        @if($challenge->tags)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach(array_slice($challenge->tags, 0, 3) as $tag)
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $tag }}</span>
                            @endforeach
                            @if(count($challenge->tags) > 3)
                            <span class="text-gray-500 text-xs">+{{ count($challenge->tags) - 3 }} more</span>
                            @endif
                        </div>
                        @endif

                        <!-- Creator Info -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-user mr-2"></i>
                                By {{ $challenge->creator->name }}
                            </div>
                            <button wire:click="selectChallenge({{ $challenge->id }})" 
                                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-code mr-1"></i>Solve Challenge
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trophy text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Code Challenges Found</h3>
                    <p class="text-gray-500 mb-4">Create your first coding challenge to get started!</p>
                    @if(auth()->user()->isInstructor() || auth()->user()->canManageUsers())
                    <button wire:click="$set('showCreateForm', true)" 
                            class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-medium">
                        Create First Challenge
                    </button>
                    @endif
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $challenges->links() }}
            </div>
        </div>
    </div>

    <!-- Create Challenge Modal -->
    @if($showCreateForm)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center animate__animated animate__fadeIn">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-screen overflow-y-auto animate__animated animate__zoomIn">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Create Code Challenge</h3>
                    <button wire:click="$set('showCreateForm', false)" 
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="createChallenge" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Challenge Title *</label>
                            <input type="text" wire:model="title" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   placeholder="Two Sum Problem">
                            @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty *</label>
                                <select wire:model="difficulty" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Points *</label>
                                <input type="number" wire:model="points" min="10" max="1000" step="10"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                @error('points') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea wire:model="description" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                  placeholder="Brief description of the challenge..."></textarea>
                        @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Problem Statement *</label>
                        <textarea wire:model="problem_statement" rows="6"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                  placeholder="Detailed problem description with constraints and requirements..."></textarea>
                        @error('problem_statement') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sample Input</label>
                            <textarea wire:model="sample_input" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                      placeholder="Example input..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sample Output</label>
                            <textarea wire:model="sample_output" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                      placeholder="Expected output..."></textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags (comma-separated)</label>
                        <input type="text" wire:model="tags" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                               placeholder="array, sorting, dynamic programming">
                    </div>

                    <div class="flex justify-end space-x-4 pt-6">
                        <button type="button" wire:click="$set('showCreateForm', false)"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Create Challenge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Submission Modal -->
    @if($showSubmissionForm && $selectedChallenge)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center animate__animated animate__fadeIn">
        <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-screen overflow-y-auto animate__animated animate__zoomIn">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">{{ $selectedChallenge->title }}</h3>
                    <button wire:click="$set('showSubmissionForm', false)" 
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Problem Description -->
                    <div>
                        <div class="mb-4">
                            <div class="flex items-center space-x-4 mb-4">
                                <span class="bg-{{ $selectedChallenge->difficulty === 'easy' ? 'green' : ($selectedChallenge->difficulty === 'medium' ? 'yellow' : 'red') }}-100 
                                           text-{{ $selectedChallenge->difficulty === 'easy' ? 'green' : ($selectedChallenge->difficulty === 'medium' ? 'yellow' : 'red') }}-800 
                                           px-3 py-1 rounded-full text-sm font-medium">
                                    {{ ucfirst($selectedChallenge->difficulty) }}
                                </span>
                                <span class="text-orange-600 font-medium">{{ $selectedChallenge->points }} points</span>
                            </div>
                            <p class="text-gray-600 mb-4">{{ $selectedChallenge->description }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Problem Statement:</h4>
                            <div class="prose prose-sm max-w-none">
                                {!! nl2br(e($selectedChallenge->problem_statement)) !!}
                            </div>
                        </div>

                        @if($selectedChallenge->sample_inputs && $selectedChallenge->sample_outputs)
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Sample Input:</h4>
                                <pre class="bg-gray-100 p-3 rounded-lg text-sm overflow-x-auto">{{ $selectedChallenge->sample_inputs[0] ?? '' }}</pre>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Sample Output:</h4>
                                <pre class="bg-gray-100 p-3 rounded-lg text-sm overflow-x-auto">{{ $selectedChallenge->sample_outputs[0] ?? '' }}</pre>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Code Editor -->
                    <div>
                        <form wire:submit.prevent="submitSolution">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Programming Language</label>
                                <select wire:model="language" 
                                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <option value="javascript">JavaScript</option>
                                    <option value="python">Python</option>
                                    <option value="php">PHP</option>
                                    <option value="java">Java</option>
                                    <option value="cpp">C++</option>
                                    <option value="csharp">C#</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Your Solution</label>
                                <textarea wire:model="code" rows="20"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent font-mono text-sm"
                                          placeholder="Write your solution here..."></textarea>
                                @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex justify-end space-x-4">
                                <button type="button" wire:click="$set('showSubmissionForm', false)"
                                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                    Submit Solution
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>