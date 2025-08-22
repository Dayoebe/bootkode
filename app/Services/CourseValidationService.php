<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CourseValidationService
{
    /**
     * Validate section data.
     *
     * @throws ValidationException
     */
    public function validateSection(string $title, ?string $description): void
    {
        Validator::make([
            'title' => $title,
            'description' => $description,
        ], [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ])->validate();
    }

    /**
     * Validate lesson data.
     *
     * @throws ValidationException
     */
    public function validateLesson(string $title, ?string $description, int $duration, string $contentType): void
    {
        Validator::make([
            'title' => $title,
            'description' => $description,
            'duration' => $duration,
            'content_type' => $contentType,
        ], [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration' => 'nullable|integer|min:0|max:1440',
            'content_type' => 'required|in:text,video,file',
        ])->validate();
    }

    /**
     * Validate lesson title.
     *
     * @throws ValidationException
     */
    public function validateLessonTitle(string $title): void
    {
        Validator::make([
            'title' => $title,
        ], [
            'title' => 'required|string|max:255',
        ])->validate();
    }

    /**
     * Validate assessment data.
     *
     * @throws ValidationException
     */
    public function validateAssessment(string $title, ?string $description, string $type, int $durationMinutes, ?string $deadline): void
    {
        Validator::make([
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'duration_minutes' => $durationMinutes,
            'deadline' => $deadline,
        ], [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:project,quiz,assignment',
            'duration_minutes' => 'nullable|integer|min:0|max:1440',
            'deadline' => 'nullable|date|after:now',
        ])->validate();
    }

    /**
     * Validate assessment title.
     *
     * @throws ValidationException
     */
    public function validateAssessmentTitle(string $title): void
    {
        Validator::make([
            'title' => $title,
        ], [
            'title' => 'required|string|max:255',
        ])->validate();
    }
}