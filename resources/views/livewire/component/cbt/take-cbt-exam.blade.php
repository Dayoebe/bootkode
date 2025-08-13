<div x-data="{ isFullScreen: @entangle('isFullScreen') }" x-init="
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('cut', e => e.preventDefault());
    document.addEventListener('paste', e => e.preventDefault());
    window.addEventListener('beforeunload', e => {
        if (isFullScreen) {
            e.preventDefault();
            e.returnValue = 'Are you sure you want to leave? Your exam progress will be lost.';
        }
    });
    document.addEventListener('keydown', e => {
        if (isFullScreen && (e.ctrlKey || e.altKey || e.key === 'F12')) {
            e.preventDefault();
        }
    });
    $watch('isFullScreen', value => {
        if (value) {
            document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    });
    @this.on('startTimer', () => {
        setInterval(() => @this.call('updateTimer'), 1000);
    });
" class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    @if(!$isExamStarted)
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-2xl w-full animate__animated animate__fadeIn">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-laptop-code mr-2"></i> {{ $exam['title'] }}
            </h1>
            <p class="text-gray-600 mb-4">{{ $exam['description'] }}</p>
            <p class="text-gray-600 mb-4">Duration: {{ $exam['duration_minutes'] }} minutes</p>
            <p class="text-gray-600 mb-4">Total Marks: {{ $exam['total_marks'] }}</p>
            <button wire:click="startExam" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-play mr-2"></i> Start Exam
            </button>
        </div>
    @else
        <div class="fixed inset-0 bg-white p-4 flex flex-col animate__animated animate__fadeIn">
            <!-- Timer -->
            <div class="bg-gray-800 text-white p-4 rounded-md flex justify-between items-center">
                <h2 class="text-lg font-bold">{{ $exam['title'] }}</h2>
                <div class="text-xl font-mono">
                    Time Remaining: <span x-text="Math.floor({{ $timeRemaining }} / 3600).toString().padStart(2, '0') + ':' + 
                                          Math.floor(({{ $timeRemaining }} % 3600) / 60).toString().padStart(2, '0') + ':' + 
                                          ({{ $timeRemaining }} % 60).toString().padStart(2, '0')"></span>
                </div>
            </div>

            <!-- Question -->
            @if(count($questions) > 0)
                <div class="flex-1 overflow-y-auto p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                    </h3>
                    <p class="text-gray-600 mb-4">{!! nl2br(e($questions[$currentQuestionIndex]['question'])) !!}</p>
                    <div class="space-y-4">
                        @foreach($questions[$currentQuestionIndex]['options'] as $index => $option)
                            <label class="flex items-center space-x-2">
                                <input type="radio" 
                                       name="answer_{{ $questions[$currentQuestionIndex]['id'] }}"
                                       value="{{ $index }}"
                                       @if(isset($answers[$questions[$currentQuestionIndex]['id']]) && $answers[$questions[$currentQuestionIndex]['id']] == $index) checked @endif
                                       wire:change="submitAnswer({{ $questions[$currentQuestionIndex]['id'] }}, {{ $index }})"
                                       class="form-radio text-blue-600">
                                <span class="text-gray-600">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation -->
                <div class="p-4 border-t border-gray-200 flex justify-between">
                    <button wire:click="previousQuestion" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50"
                            {{ $currentQuestionIndex == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-arrow-left mr-2"></i> Previous
                    </button>
                    <button wire:click="nextQuestion" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200 disabled:opacity-50"
                            {{ $currentQuestionIndex == count($questions) - 1 ? 'disabled' : '' }}>
                        <i class="fas fa-arrow-right mr-2"></i> Next
                    </button>
                    <button wire:click="submitExam" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200">
                        <i class="fas fa-check mr-2"></i> Submit Exam
                    </button>
                </div>
            @else
                <p class="text-gray-500 text-center">No questions available for this exam.</p>
            @endif
        </div>
    @endif
</div>