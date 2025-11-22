{{ $results['results']['passed'] ? 'CONGRATULATIONS!' : 'EXAM RESULTS' }}
{{ str_repeat('=', 60) }}

{{ $results['assessment']['title'] }}

Dear {{ $results['student']['name'] }},

@if($results['results']['passed'])
Excellent work! You have successfully passed the exam.
@else
Thank you for completing the exam. Review the feedback below to improve for your next attempt.
@endif

YOUR SCORE
{{ str_repeat('-', 60) }}
Score: {{ $results['results']['percentage'] }}%
Pass Mark: {{ $results['assessment']['pass_percentage'] }}%
Status: {{ $results['results']['passed'] ? 'PASSED ✓' : 'NOT PASSED' }}

STATISTICS
{{ str_repeat('-', 60) }}
Correct Answers: {{ $results['results']['correct_answers'] }} / {{ $results['results']['total_questions'] }}
Accuracy Rate: {{ $results['statistics']['accuracy_rate'] }}%
Time Spent: {{ $results['statistics']['time_spent'] }}
Average Time per Question: {{ $results['statistics']['avg_time_per_question'] }}

@if(count($results['insights']) > 0)
PERFORMANCE INSIGHTS
{{ str_repeat('-', 60) }}
@foreach($results['insights'] as $insight)
{{ $insight['icon'] }} {{ $insight['title'] }}
   {{ $insight['message'] }}

@endforeach
@endif

@if($results['certificate'])
CERTIFICATE OF COMPLETION
{{ str_repeat('-', 60) }}
Congratulations! You've earned a certificate for passing this assessment.
Certificate ID: {{ $results['certificate']['id'] }}

@endif

@if(count($results['questions']['incorrect']) > 0)
QUESTIONS YOU MISSED (Review These!)
{{ str_repeat('-', 60) }}
@foreach(array_slice($results['questions']['incorrect'], 0, 5) as $index => $question)

Question {{ $index + 1 }}: {{ $question['type'] }} ({{ $question['points'] }} pts)
{{ $question['text'] }}

Your Answer: {{ $question['user_answer'] }}
Correct Answer: {{ $question['correct_answer'] }}

@if($question['explanation'])
Explanation: {{ $question['explanation'] }}
@endif
{{ str_repeat('-', 60) }}
@endforeach

@if(count($results['questions']['incorrect']) > 5)
... and {{ count($results['questions']['incorrect']) - 5 }} more questions.
View all in your dashboard: {{ $results['results_url'] }}
@endif
@endif

@if(count($results['questions']['correct']) > 0)
QUESTIONS YOU GOT RIGHT
{{ str_repeat('-', 60) }}
Great job! You answered {{ count($results['questions']['correct']) }} question(s) correctly.
@endif

RECOMMENDED NEXT STEPS
{{ str_repeat('-', 60) }}
@foreach(array_slice($results['recommendations'], 0, 3) as $recommendation)
{{ $recommendation['icon'] }} {{ $recommendation['title'] }}
   {{ $recommendation['description'] }}

@endforeach

@if(!$results['results']['passed'])
RETAKE THE EXAM
Visit: {{ $results['exam_url'] }}

@endif
VIEW FULL RESULTS
Visit: {{ $results['results_url'] }}

{{ str_repeat('=', 60) }}

@if($results['results']['passed'])
Once again, congratulations on your achievement! Keep up the excellent work!
@else
Remember, every attempt is a learning opportunity. Review the explanations above and you'll do even better next time!
@endif

Best regards,
{{ config('app.name') }} Team

---
Exam completed on {{ $results['submitted_at'] }}
© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.

Unsubscribe: {{ config('app.url') }}/unsubscribe