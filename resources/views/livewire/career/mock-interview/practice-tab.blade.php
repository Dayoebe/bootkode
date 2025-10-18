{{-- resources/views/livewire/career/mock-interview/partials/practice-tab.blade.php --}}

<div class="space-y-8">
    <div class="bg-themed-secondary rounded-2xl shadow-lg p-8">
        <h3 class="text-2xl font-bold text-themed-primary mb-6">Quick Practice Session</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="space-y-4">
                <label class="block text-sm font-semibold text-themed-primary">Interview Type</label>
                <select wire:model.live="type"
                    class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-themed-secondary text-themed-primary">
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
                    class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-themed-secondary text-themed-primary">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="expert">Expert</option>
                </select>
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-semibold text-themed-primary">Format</label>
                <select wire:model.live="format"
                    class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-themed-secondary text-themed-primary">
                    <option value="text">Text Only</option>
                    <option value="voice">Voice Recording</option>
                    <option value="video">Video Recording</option>
                    <option value="mixed">Mixed Format</option>
                </select>
            </div>
        </div>

        <div class="text-center">
            <button wire:click="startQuickPractice"
                class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-4 rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all font-semibold text-lg shadow-lg">
                <i class="fas fa-play mr-2"></i> Start Practice Session
            </button>
        </div>
    </div>

    <!-- Sample Questions Preview -->
    <div class="bg-themed-secondary rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-themed-primary mb-6">
            Sample Questions for {{ ucfirst($type) }} Interview
        </h3>
        <div class="space-y-4">
            @php
                $sampleQuestions = match ($type) {
                    'technical' => $technicalQuestions,
                    'behavioral' => $behavioralQuestions,
                    'system_design' => $systemDesignQuestions,
                    default => $technicalQuestions
                };
            @endphp
            @foreach(array_slice($sampleQuestions, 0, 5) as $index => $question)
                <div class="p-4 bg-themed-tertiary rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <span class="bg-blue-100 text-blue-600 text-sm font-medium px-2 py-1 rounded-full mr-3 mt-0.5">
                            {{ $index + 1 }}
                        </span>
                        <p class="text-themed-primary flex-1">{{ $question }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <p class="text-sm text-themed-secondary">
                <i class="fas fa-info-circle mr-2"></i>
                These are sample questions. Your actual practice session will include 
                {{ match($difficulty_level) {
                    'beginner' => '5',
                    'intermediate' => '8',
                    'advanced' => '12',
                    'expert' => '15',
                    default => '8'
                } }} 
                randomly selected questions.
            </p>
        </div>
    </div>
</div>