<?php

namespace App\Services;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Messaging\DirectConversation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class DirectMessagingService
{
    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function getOrCreateCourseConversation(Course $course, User $student): DirectConversation
    {
        $course->loadMissing('instructor');

        if (!$course->instructor) {
            throw ValidationException::withMessages([
                'course' => 'This course does not have an instructor assigned yet.',
            ]);
        }

        if ((int) $course->instructor_id === (int) $student->id) {
            throw ValidationException::withMessages([
                'course' => 'You are the instructor for this course.',
            ]);
        }

        if (!$student->enrollments()->where('course_id', $course->id)->exists()) {
            throw new AuthorizationException('Only enrolled students can message this course instructor.');
        }

        if (!$this->instructorAcceptsCourseMessages($course->instructor)) {
            throw ValidationException::withMessages([
                'course' => 'This instructor is not accepting course messages right now.',
            ]);
        }

        return DirectConversation::firstOrCreate(
            [
                'course_id' => $course->id,
                'student_id' => $student->id,
                'instructor_id' => $course->instructor_id,
            ],
            [
                'last_message_at' => null,
            ]
        );
    }

    public function instructorAcceptsCourseMessages(User $instructor): bool
    {
        $privacy = $instructor->privacy_settings ?? [];

        if (!is_array($privacy)) {
            $privacy = json_decode((string) $privacy, true) ?: [];
        }

        return $privacy['allow_course_messages'] ?? true;
    }

    /**
     * @throws AuthorizationException
     */
    public function ensureParticipant(User $user, DirectConversation $conversation): void
    {
        if (!$conversation->isParticipant($user)) {
            throw new AuthorizationException('You do not have access to this conversation.');
        }
    }
}
