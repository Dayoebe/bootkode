<!-- resources/views/emails/cbt/weekly-summary.blade.php -->
@component('mail::message')
# 📊 Your Weekly CBT Summary

Hello **{{ $user->name }}**,

Here's your learning activity summary for the week of {{ $weekStart->format('M j') }} - {{ $weekEnd->format('M j, Y') }}:

## This Week's Performance

| Metric | Count | Change |
|--------|-------|--------|
| **Exams Taken** | {{ $stats['exams_taken'] }} | @if($stats['exams_change'] >= 0) ↗️ +{{ $stats['exams_change'] }} @else ↘️ {{ $stats['exams_change'] }} @endif |
| **Average Score** | {{ number_format($stats['avg_score'], 1) }}% | @if($stats['score_change'] >= 0) ↗️ +{{ number_format($stats['score_change'], 1) }}% @else ↘️ {{ number_format($stats['score_change'], 1) }}% @endif |
| **Study Time** | {{ $stats['study_time'] }} | @if($stats['time_change'] >= 0) ↗️ +{{ $stats['time_change'] }} @else ↘️ {{ $stats['time_change'] }} @endif |
| **Achievements** | {{ $stats['new_achievements'] }} | New this week! |

@if($stats['exams_taken'] > 0)
## Recent Exam Results

@foreach($recentResults as $result)
**{{ $result->exam->title }}**
- Score: {{ number_format($result->percentage_score, 1) }}% @if($result->passed) ✅ @else ❌ @endif
- Grade: {{ $result->grade }}
- Completed: {{ $result->completed_at->format('M j') }}

@endforeach
@endif

@if($upcomingExams->count() > 0)
## Upcoming Opportunities

@foreach($upcomingExams as $exam)
**{{ $exam->title }}**
- Course: {{ $exam->course->title }}
- @if($exam->end_date) Deadline: {{ $exam->end_date->format('M j, Y') }} @else Available now @endif

@endforeach

@component('mail::button', ['url' => route('cbt.center')])
View Available Exams
@endcomponent
@endif

@if($stats['study_streak'] > 0)
🔥 **Study Streak:** {{ $stats['study_streak'] }} days - Keep it up!
@endif

## Motivational Quote
> "The expert in anything was once a beginner." - Helen Hayes

Keep learning, keep growing!

Best regards,<br>
{{ config('app.name') }} CBT Team
@endcomponent