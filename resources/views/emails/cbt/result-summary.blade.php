<!-- resources/views/emails/cbt/result-summary.blade.php -->
@component('mail::message')
# @if($result->passed) 🎉 Congratulations! @else 📊 Your Exam Results @endif

Hello **{{ $user->name }}**,

Your exam results for **{{ $exam->title }}** are now available.

@if($result->passed)
    🎉 **Congratulations!** You have successfully passed the exam with a score of
    **{{ number_format($result->percentage_score, 1) }}%**.

    @if($exam->exam_type === 'certification')
        ✅ **Certificate Eligible:** You are now eligible for certification. Your certificate will be processed within 24-48
        hours.
    @endif
@else
    Your score: **{{ number_format($result->percentage_score, 1) }}%** (Required: {{ $exam->pass_percentage }}%)

    @php
        $remainingAttempts = $exam->max_attempts - $result->attempt_number;
    @endphp

    @if($remainingAttempts > 0)
        💪 **Don't give up!** You have **{{ $remainingAttempts }}** more attempt(s) remaining. Review the material and try again
        when you're ready.
    @endif
@endif

## Exam Summary

| Detail | Result |
|--------|--------|
| **Final Score** | {{ number_format($result->percentage_score, 1) }}% |
| **Grade** | {{ $result->grade }} |
| **Status** | @if($result->passed) ✅ PASSED @else ❌ FAILED @endif |
| **Correct Answers** | {{ $result->correct_answers }}/{{ $result->total_questions }} |
| **Time Spent** | {{ gmdate('H:i:s', $result->time_spent_seconds) }} |
| **Attempt Number** | #{{ $result->attempt_number }} |
| **Completed** | {{ $result->completed_at->format('M j, Y \a\t g:i A') }} |

@if($result->auto_submitted)
    ⚠️ **Note:** This exam was automatically submitted due to time expiration.
@endif

## Performance Breakdown

@php
    $performance = [
        'Excellent' => $result->percentage_score >= 90,
        'Good' => $result->percentage_score >= 80 && $result->percentage_score < 90,
        'Satisfactory' => $result->percentage_score >= 70 && $result->percentage_score < 80,
        'Needs Improvement' => $result->percentage_score < 70
    ];
    $currentPerformance = array_search(true, $performance);
@endphp

**Performance Level:** {{ $currentPerformance }}

@if($currentPerformance === 'Needs Improvement')
    **Recommended Actions:**
    - Review the course materials thoroughly
    - Focus on areas where you got questions wrong
    - Consider taking practice quizzes
    - Reach out to instructors for clarification on difficult topics
@elseif($currentPerformance === 'Satisfactory')
    **Great job!** You've met the minimum requirements. Consider reviewing challenging areas to aim for an even higher
    score.
@elseif($currentPerformance === 'Good')
    **Well done!** You've shown strong understanding of the material.
@else
    **Outstanding performance!** You've demonstrated excellent mastery of the subject.
@endif

@if($exam->show_correct_answers || $exam->show_explanations)
    @component('mail::button', ['url' => route('cbt.result.detailed', $result->session_id)])
    View Detailed Results
    @endcomponent
@else
    @component('mail::button', ['url' => route('cbt.results')])
    View All Results
    @endcomponent
@endif

---

@if(!$result->passed && $remainingAttempts > 0)
    **Ready to try again?**

    @component('mail::button', ['url' => route('cbt.exam', $exam->slug)])
    Retake Exam
    @endcomponent
@endif

@if($result->passed && $exam->exam_type === 'certification')
    **Certificate Information:**
    Your digital certificate will be automatically generated and sent to you within 24-48 hours. You can also download it
    from your dashboard once it's ready.
@endif

Thank you for using our CBT platform. We're here to support your learning journey!

Best regards,<br>
{{ config('app.name') }} CBT Team

---
<small>
    **Exam Details:**<br>
    Course: {{ $exam->course->title ?? 'General' }}<br>
    Exam Type: {{ ucfirst(str_replace('_', ' ', $exam->exam_type)) }}<br>
    Difficulty: {{ ucfirst($exam->difficulty_level) }}<br>
    Session ID: {{ $result->session_id }}
</small>
@endcomponent