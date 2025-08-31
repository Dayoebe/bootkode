
<!-- resources/views/emails/cbt/reminder.blade.php -->
@component('mail::message')
# ⏰ Exam Reminder: {{ $exam->title }}

Hello **{{ $user->name }}**,

@if($reminderType === 'upcoming')
This is a friendly reminder that you have an upcoming exam opportunity:

**{{ $exam->title }}**

📚 **Course:** {{ $exam->course->title }}<br>
⏱️ **Duration:** {{ $exam->formatted_duration }}<br>
📅 **Available from:** {{ $exam->start_date->format('M j, Y \a\t g:i A') }}<br>
@if($exam->end_date)
🕐 **Deadline:** {{ $exam->end_date->format('M j, Y \a\t g:i A') }}
@endif

@elseif($reminderType === 'deadline_approaching')
⚠️ **Important:** The deadline for your exam is approaching!

**{{ $exam->title }}**

🚨 **Deadline:** {{ $exam->end_date->format('M j, Y \a\t g:i A') }}<br>
⏰ **Time remaining:** {{ $exam->end_date->diffForHumans() }}

Don't miss this opportunity to complete your exam!

@elseif($reminderType === 'last_chance')
🚨 **Final Notice:** This is your last chance to take this exam!

**{{ $exam->title }}**

🚨 **Deadline:** {{ $exam->end_date->format('M j, Y \a\t g:i A') }}<br>
⏰ **Time remaining:** {{ $exam->end_date->diffForHumans() }}

**Act now** - this opportunity won't be available after the deadline!
@endif

## Exam Information

| Detail | Information |
|--------|-------------|
| **Questions** | {{ $exam->total_questions }} |
| **Duration** | {{ $exam->formatted_duration }} |
| **Pass Percentage** | {{ $exam->pass_percentage }}% |
| **Attempts Allowed** | {{ $exam->max_attempts }} |
| **Type** | {{ ucfirst(str_replace('_', ' ', $exam->exam_type)) }} |
| **Difficulty** | {{ ucfirst($exam->difficulty_level) }} |

@if($exam->instructions)
## Instructions
{{ $exam->instructions }}
@endif

@component('mail::button', ['url' => route('cbt.exam', $exam->slug)])
Take Exam Now
@endcomponent

**Tips for Success:**
- Ensure you have a stable internet connection
- Find a quiet, distraction-free environment
- Review your course materials before starting
- Manage your time effectively during the exam

Good luck with your exam!

Best regards,<br>
{{ config('app.name') }} CBT Team
@endcomponent
