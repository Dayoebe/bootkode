{{-- resources/views/livewire/career/mock-interview/partials/practice-tab.blade.php --}}

<div class="space-y-8">
    <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-8">
        <h3 class="text-2xl font-bold text-themed-primary mb-6">Quick Practice Session</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="space-y-4">
                <label class="block text-sm font-semibold text-themed-primary">Interview Type</label>
                <select wire:model.live="type"
                    class="w-full px-4 py-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    <option value="technical">Technical</option>
                    <option value="behavioral">Behavioral</option>
                    <option value="system_design">System Design</option>
                    <option value="coding">Coding</option>
                    <option value="hr">HR</option>
                </select>
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-semibold text-themed-primary">Difficulty Level</label>
                <select wire:model.live="difficulty_level"
                    class="w-full px-4 py-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="expert">Expert</option>
                </select>
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-semibold text-themed-primary">Format</label>
                <select wire:model.live="format"
                    class="w-full px-4 py-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    <option value="text">Text Only</option>
                    <option value="voice">Voice Recording</option>
                    <option value="video">Video Recording</option>
                    <option value="mixed">Mixed Format</option>
                </select>
            </div>
        </div>

        <div class="text-center">
            <button wire:click="startQuickPractice"
                class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-xl transition-colors font-semibold text-lg shadow-lg">
                <i class="fas fa-play mr-2"></i> Start Practice Session
            </button>
        </div>
    </div>

    <!-- Sample Questions Preview -->
    <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-themed-primary mb-6">
            Sample Questions for {{ ucfirst(str_replace('_', ' ', $type)) }} Interview
        </h3>
        <div class="space-y-4">
            @php
                $sampleQuestions = [];
                
                match ($type) {
                    'technical' => $sampleQuestions = [
                        'Explain the difference between REST and GraphQL APIs',
                        'What is the difference between SQL and NoSQL databases?',
                        'How does caching improve application performance?',
                        'Explain the concept of microservices architecture',
                        'What are the SOLID principles in object-oriented design?'
                    ],
                    'behavioral' => $sampleQuestions = [
                        'Tell me about a time when you had to deal with a difficult team member',
                        'Describe a situation where you had to meet a tight deadline',
                        'Give an example of when you showed leadership',
                        'Tell me about a project where you failed and what you learned',
                        'How do you handle constructive criticism?'
                    ],
                    'system_design' => $sampleQuestions = [
                        'Design a URL shortening service like TinyURL',
                        'How would you design a real-time messaging application?',
                        'Design a social media feed system',
                        'How would you design an e-commerce platform?',
                        'Design a video streaming service like YouTube'
                    ],
                    'coding' => $sampleQuestions = [
                        'Reverse a linked list',
                        'Find the longest substring without repeating characters',
                        'Implement a binary search algorithm',
                        'Merge two sorted arrays',
                        'Find all permutations of a string'
                    ],
                    'hr' => $sampleQuestions = [
                        'What are your greatest strengths and weaknesses?',
                        'Why are you interested in this position?',
                        'Where do you see yourself in 5 years?',
                        'Tell me about your career progression',
                        'What is your expected salary range?'
                    ],
                    default => $sampleQuestions = [
                        'Sample question 1',
                        'Sample question 2',
                        'Sample question 3',
                        'Sample question 4',
                        'Sample question 5'
                    ]
                };
            @endphp
            
            @foreach($sampleQuestions as $index => $question)
                <div class="p-4 bg-themed-tertiary rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <span class="bg-accent-themed-primary/10 text-accent-themed-primary text-sm font-medium px-2 py-1 rounded-full mr-3 mt-0.5 whitespace-nowrap">
                            {{ $index + 1 }}
                        </span>
                        <p class="text-themed-primary flex-1">{{ $question }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 p-4 bg-accent-themed-primary/10 border border-accent-themed-primary/20 rounded-lg">
            <p class="text-sm text-themed-secondary">
                <i class="fas fa-info-circle mr-2 text-accent-themed-primary"></i>
                These are sample questions. Your actual practice session will include 
                @php
                    $questionCount = match($difficulty_level) {
                        'beginner' => '5',
                        'intermediate' => '8',
                        'advanced' => '12',
                        'expert' => '15',
                        default => '8'
                    };
                @endphp
                {{ $questionCount }} 
                randomly selected questions.
            </p>
        </div>
    </div>
</div>