<div class="w-full px-4 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6 pt-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-100 mb-0">
                <i class="fas fa-chart-bar mr-2"></i>My CBT Results
            </h1>
            <p class="text-gray-600 dark:text-gray-400">View your CBT assessment results and performance</p>
        </div>
        @if($viewDetails)
            <button wire:click="closeDetails" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </button>
        @endif
    </div>

    @if(!$viewDetails)
        <!-- Results Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700">
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 rounded-t-lg">
                <h6 class="text-lg font-semibold text-blue-600 dark:text-blue-400 m-0">Your CBT Assessments</h6>
            </div>
            <div class="p-6">
                @if($userAssessments->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($userAssessments as $assessment)
                            @php
                                $attempts = $this->getAttemptsForAssessment($assessment);
                                $bestAttempt = $attempts->sortByDesc('percentage')->first();
                                $latestAttempt = $attempts->first();
                            @endphp
                            <div class="h-full">
                                <div class="bg-white dark:bg-gray-700 border-l-4 {{ $bestAttempt['passed'] ? 'border-green-500' : 'border-red-500' }} rounded-lg shadow-md h-full border border-gray-200 dark:border-gray-600">
                                    <div class="p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <h6 class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $assessment->title }}</h6>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $bestAttempt['passed'] ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                                {{ $bestAttempt['passed'] ? 'PASSED' : 'FAILED' }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ $assessment->description }}</p>
                                        
                                        <div class="grid grid-cols-3 gap-4 text-center mb-4">
                                            <div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">Best Score</div>
                                                <div class="text-lg font-semibold {{ $bestAttempt['passed'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $bestAttempt['percentage'] }}%
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">Attempts</div>
                                                <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $attempts->count() }}</div>
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">Last Attempt</div>
                                                <div class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                                    {{ $latestAttempt['submitted_at']->format('M d') }}
                                                </div>
                                            </div>
                                        </div>

                                        <button wire:click="viewAssessmentDetails({{ $assessment->id }})" 
                                                class="w-full bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-600 px-4 py-2 rounded-lg transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $userAssessments->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-clipboard-list text-6xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <h5 class="text-xl text-gray-600 dark:text-gray-400 mb-2">No CBT Results Yet</h5>
                        <p class="text-gray-500 dark:text-gray-500 mb-6">You haven't taken any CBT assessments yet.</p>
                        <a href="{{ route('cbt.exam') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                            <i class="fas fa-pencil-alt mr-2"></i>Take CBT Exam
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Detailed View -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg animate-fade-in border dark:border-gray-700">
            <div class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-4 rounded-t-lg">
                <h5 class="text-xl font-semibold m-0">
                    <i class="fas fa-chart-line mr-2"></i>{{ $selectedAssessment->title }} - Detailed Results
                </h5>
            </div>
            <div class="p-6">
                @php
                    $attempts = $this->getAttemptsForAssessment($selectedAssessment);
                    $bestAttempt = $attempts->sortByDesc('percentage')->first();
                @endphp

                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="text-center">
                        <div class="border rounded-lg p-6 {{ $bestAttempt['passed'] ? 'border-green-300 dark:border-green-600 bg-green-50 dark:bg-green-900/30' : 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/30' }}">
                            <div class="text-3xl font-bold {{ $bestAttempt['passed'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mb-2">
                                {{ $bestAttempt['percentage'] }}%
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Best Score</div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-gray-50 dark:bg-gray-900">
                            <div class="text-2xl font-bold text-blue-500 dark:text-blue-400 mb-2">{{ $selectedAssessment->questions->count() }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Questions</div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-gray-50 dark:bg-gray-900">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mb-2">{{ $selectedAssessment->pass_percentage }}%</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Pass Mark</div>
                        </div>
                    </div>
                </div>

                <!-- Attempts History -->
                <h6 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Attempt History</h6>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Attempt</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Points</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($attempts as $attempt)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full">#{{ $attempt['attempt_number'] }}</span>
                                        @if($attempt === $bestAttempt)
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 rounded-full ml-1">Best</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold {{ $attempt['passed'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $attempt['percentage'] }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $attempt['total_points'] }} / {{ $attempt['max_points'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-sm font-medium rounded-full {{ $attempt['passed'] ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                            {{ $attempt['passed'] ? 'PASSED' : 'FAILED' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $attempt['submitted_at']->format('M d, Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button wire:click="viewAttemptDetails({{ $attempt['attempt_number'] }})" 
                                                class="bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-600 px-3 py-1 rounded-lg text-sm transition-colors duration-200">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Attempt Details Modal -->
                @if($selectedAttempt)
                    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black dark:bg-opacity-70 z-50 flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-7xl w-full max-h-screen overflow-hidden animate-fade-in-down border dark:border-gray-700">
                            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h5 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                    <i class="fas fa-file-alt mr-2"></i>
                                    Attempt #{{ $selectedAttempt['attempt_number'] }} Details
                                </h5>
                                <button type="button" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300" wire:click="$set('selectedAttempt', null)">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            <div class="p-6 overflow-y-auto" style="max-height: 70vh;">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                                    <div class="text-center">
                                        <div class="border rounded-lg p-6 bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700">
                                            <div class="text-2xl font-bold {{ $selectedAttempt['passed'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $selectedAttempt['percentage'] }}%
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Final Score</div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-gray-50 dark:bg-gray-900">
                                            <div class="text-xl font-semibold text-blue-600 dark:text-blue-400">{{ $selectedAttempt['correct_answers'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Correct</div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-gray-50 dark:bg-gray-900">
                                            <div class="text-xl font-semibold text-red-600 dark:text-red-400">{{ $selectedAttempt['total_questions'] - $selectedAttempt['correct_answers'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Incorrect</div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-gray-50 dark:bg-gray-900">
                                            <div class="text-xl font-semibold text-blue-500 dark:text-blue-400">{{ $selectedAttempt['total_points'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Points</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Question-by-Question Review -->
                                <h6 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Question Review</h6>
                                @foreach($selectedAttempt['answers'] as $questionId => $answer)
                                    @php $question = $answer->question; @endphp
                                    <div class="border {{ $answer->is_correct ? 'border-green-300 dark:border-green-600' : 'border-red-300 dark:border-red-600' }} rounded-lg mb-4">
                                        <div class="flex justify-between items-center px-4 py-3 border-b {{ $answer->is_correct ? 'border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-900/30' : 'border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-900/30' }}">
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">Question {{ $loop->iteration }}</span>
                                            <div class="flex space-x-2">
                                                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $answer->is_correct ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                                    {{ $answer->is_correct ? 'Correct' : 'Incorrect' }}
                                                </span>
                                                <span class="px-3 py-1 text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full">{{ $answer->points_earned }}/{{ $question->points }} pts</span>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <p class="font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $question->question_text }}</p>
                                            
                                            @if($question->question_type === 'multiple_choice')
                                                <div class="ml-4">
                                                    @foreach($question->options as $index => $option)
                                                        <div class="mb-2">
                                                            <span class="mr-2">{{ chr(65 + $index) }}.</span>
                                                            <span class="@if($answer->answer == $index) font-semibold 
                                                                  @if($answer->is_correct) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif
                                                                  @elseif(in_array($index, $question->correct_answers)) text-green-600 dark:text-green-400 @else text-gray-700 dark:text-gray-300 @endif">
                                                                {{ $option }}
                                                                @if($answer->answer == $index)
                                                                    <i class="fas fa-arrow-left ml-2 text-gray-400 dark:text-gray-500"></i>
                                                                @endif
                                                                @if(in_array($index, $question->correct_answers))
                                                                    <i class="fas fa-check ml-2 text-green-600 dark:text-green-400"></i>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif($question->question_type === 'true_false')
                                                <div class="ml-4">
                                                    <div class="mb-2">
                                                        <span class="mr-2">A.</span>
                                                        <span class="@if($answer->answer == 0) font-semibold 
                                                              @if($answer->is_correct) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif
                                                              @elseif(in_array(0, $question->correct_answers)) text-green-600 dark:text-green-400 @else text-gray-700 dark:text-gray-300 @endif">
                                                            True
                                                            @if($answer->answer == 0) <i class="fas fa-arrow-left ml-2 text-gray-400 dark:text-gray-500"></i> @endif
                                                            @if(in_array(0, $question->correct_answers)) <i class="fas fa-check ml-2 text-green-600 dark:text-green-400"></i> @endif
                                                        </span>
                                                    </div>
                                                    <div class="mb-2">
                                                        <span class="mr-2">B.</span>
                                                        <span class="@if($answer->answer == 1) font-semibold 
                                                              @if($answer->is_correct) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif
                                                              @elseif(in_array(1, $question->correct_answers)) text-green-600 dark:text-green-400 @else text-gray-700 dark:text-gray-300 @endif">
                                                            False
                                                            @if($answer->answer == 1) <i class="fas fa-arrow-left ml-2 text-gray-400 dark:text-gray-500"></i> @endif
                                                            @if(in_array(1, $question->correct_answers)) <i class="fas fa-check ml-2 text-green-600 dark:text-green-400"></i> @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="ml-4">
                                                    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                                        <div class="font-semibold text-gray-700 dark:text-gray-300 mb-1">Your Answer:</div>
                                                        <div class="text-gray-800 dark:text-gray-200">{{ $answer->formatted_answer }}</div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($question->explanation)
                                                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border dark:border-gray-700">
                                                    <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">Explanation:</div>
                                                    <div class="text-sm text-gray-700 dark:text-gray-300">{{ $question->explanation }}</div>
                                                </div>
                                            @endif

                                            @if($answer->feedback)
                                                <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                                                    <div class="text-sm font-semibold text-yellow-700 dark:text-yellow-300 mb-1">Instructor Feedback:</div>
                                                    <div class="text-sm text-yellow-700 dark:text-yellow-300">{{ $answer->feedback }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .animate-fade-in-down {
            animation: fadeInDown 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInDown {
            from { 
                opacity: 0;
                transform: translateY(-20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Smooth transitions for dark mode */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</div>