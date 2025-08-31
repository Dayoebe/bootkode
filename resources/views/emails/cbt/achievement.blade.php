
<!-- resources/views/emails/cbt/achievement.blade.php -->
@component('mail::message')
# 🏆 Achievement Unlocked!

Congratulations **{{ $user->name }}**!

You've earned a new achievement:

@component('mail::panel')
{{ $achievement->achievement_icon }} **{{ $achievement->achievement_name }}**

{{ $achievement->achievement_description }}

*Earned on {{ $achievement->earned_at->format('M j, Y \a\t g:i A') }}*
@endcomponent

## Your Learning Progress

This achievement reflects your dedication and progress in your learning journey. Keep up the excellent work!

**Achievement Details:**
- **Category:** {{ ucfirst($achievement->achievement_type) }}
- **Value:** {{ $achievement->achievement_value }}
- **Date Earned:** {{ $achievement->earned_at->format('F j, Y') }}

@component('mail::button', ['url' => route('student.achievements')])
View All Achievements
@endcomponent

**What's Next?**

Continue your learning journey to unlock even more achievements:
- Complete more exams to earn performance badges
- Maintain study streaks for consistency rewards
- Explore advanced courses for expertise recognition

You're doing great! Keep learning and achieving.

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent
