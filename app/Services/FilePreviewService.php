<?php

namespace App\Services;

use App\Data\FilePreview;
use App\Models\Assessment\LearningSession;
use App\Models\Community\Feedback;
use App\Models\Community\SupportTicket;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\LearningMaterial;
use App\Models\Learning\Lesson;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class FilePreviewService
{
    public const SOURCES = [
        'lesson-document', 'lesson-image', 'lesson-main-image', 'lesson-audio', 'lesson-video', 'lesson-file',
        'course-document', 'learning-material', 'assessment-submission', 'support-attachment',
        'feedback-attachment',
    ];

    private const PREVIEW_KINDS = [
        'pdf' => 'pdf', 'docx' => 'document', 'txt' => 'text', 'md' => 'markdown',
        'pptx' => 'presentation', 'xlsx' => 'spreadsheet', 'csv' => 'spreadsheet',
        'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'webp' => 'image',
        'gif' => 'image', 'svg' => 'image', 'mp3' => 'audio', 'wav' => 'audio',
        'ogg' => 'audio', 'mp4' => 'video', 'webm' => 'video', 'mov' => 'video',
    ];

    private const UNSUPPORTED_REASONS = [
        'doc' => 'Legacy Word files cannot be rendered reliably in this browser. Download the original or ask the author for a DOCX copy.',
        'rtf' => 'This RTF file cannot be rendered reliably without changing its content. Download the original file.',
        'ppt' => 'Legacy PowerPoint files cannot be rendered reliably in this browser. Download the original or ask the author for a PPTX copy.',
        'xls' => 'Legacy Excel files cannot be rendered reliably in this browser. Download the original or ask the author for an XLSX or CSV copy.',
    ];

    private const MIME_TYPES = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword', 'application/vnd.ms-office', 'application/x-ole-storage', 'application/octet-stream'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
        'txt' => ['text/plain', 'application/octet-stream'],
        'md' => ['text/plain', 'text/markdown', 'application/octet-stream'],
        'rtf' => ['application/rtf', 'text/rtf', 'text/plain', 'application/octet-stream'],
        'ppt' => ['application/vnd.ms-powerpoint', 'application/vnd.ms-office', 'application/x-ole-storage', 'application/octet-stream'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip', 'application/octet-stream'],
        'xls' => ['application/vnd.ms-excel', 'application/vnd.ms-office', 'application/x-ole-storage', 'application/octet-stream'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'],
        'csv' => ['text/csv', 'text/plain', 'application/csv', 'application/octet-stream'],
        'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'png' => ['image/png'],
        'webp' => ['image/webp'], 'gif' => ['image/gif'], 'svg' => ['image/svg+xml', 'text/plain'],
        'mp3' => ['audio/mpeg', 'audio/mp3'], 'wav' => ['audio/wav', 'audio/x-wav'],
        'ogg' => ['audio/ogg', 'video/ogg', 'application/ogg'], 'mp4' => ['video/mp4'],
        'webm' => ['video/webm'], 'mov' => ['video/quicktime'],
    ];

    public function resolve(User $viewer, string $source, string $record, string $file): FilePreview
    {
        abort_unless(in_array($source, self::SOURCES, true), 404);

        $resolved = match ($source) {
            'lesson-document', 'lesson-image', 'lesson-audio', 'lesson-video' => $this->lessonAsset($viewer, $source, $record, $file),
            'lesson-main-image' => $this->lessonMainImage($viewer, $record),
            'lesson-file' => $this->lessonPrimaryFile($viewer, $record),
            'course-document' => $this->courseDocument($viewer, $record, $file),
            'learning-material' => $this->learningMaterial($viewer, $record),
            'assessment-submission' => $this->assessmentSubmission($viewer, $record, $file),
            'support-attachment' => $this->supportAttachment($viewer, $record),
            'feedback-attachment' => $this->feedbackAttachment($viewer, $record),
        };

        return $this->inspect($source, $record, $file, ...$resolved);
    }

    private function lessonAsset(User $viewer, string $source, string $record, string $file): array
    {
        abort_unless(ctype_digit($record) && ctype_digit($file), 404);
        $lesson = Lesson::findOrFail((int) $record);
        app(CbtAttemptService::class)->authorizeLesson($viewer, $lesson);
        $property = match ($source) {
            'lesson-document' => 'documents', 'lesson-image' => 'images',
            'lesson-audio' => 'audios', 'lesson-video' => 'videos',
        };
        $asset = $lesson->{'get'.ucfirst($property).'Array'}()[(int) $file] ?? null;
        abort_unless(is_array($asset), 404);
        $prefix = 'lessons/'.match ($property) {
            'documents' => 'documents/', 'images' => 'images/', 'audios' => 'audios/', 'videos' => 'videos/',
        };

        return $this->storedAsset('public', $asset['path'] ?? null, $asset['name'] ?? null, [$prefix]);
    }

    private function lessonPrimaryFile(User $viewer, string $record): array
    {
        abort_unless(ctype_digit($record), 404);
        $lesson = Lesson::findOrFail((int) $record);
        app(CbtAttemptService::class)->authorizeLesson($viewer, $lesson);

        return $this->storedAsset('public', $lesson->file_path, basename((string) $lesson->file_path), ['lessons/']);
    }

    private function lessonMainImage(User $viewer, string $record): array
    {
        abort_unless(ctype_digit($record), 404);
        $lesson = Lesson::findOrFail((int) $record);
        app(CbtAttemptService::class)->authorizeLesson($viewer, $lesson);

        return $this->storedAsset('public', $lesson->image_path, basename((string) $lesson->image_path), ['lessons/']);
    }

    private function courseDocument(User $viewer, string $record, string $file): array
    {
        abort_unless(ctype_digit($record) && ctype_digit($file), 404);
        $course = Course::findOrFail((int) $record);
        $this->authorizeCourse($viewer, $course);
        $asset = $course->getDocumentsArray()[(int) $file] ?? null;
        abort_unless(is_array($asset), 404);

        return $this->storedAsset('public', $asset['path'] ?? null, $asset['name'] ?? null, ['courses/', 'course/']);
    }

    private function learningMaterial(User $viewer, string $record): array
    {
        abort_unless(ctype_digit($record), 404);
        $material = LearningMaterial::findOrFail((int) $record);
        abort_unless($material->canBeViewedBy($viewer), 403);
        $privileged = (int) $material->created_by === (int) $viewer->id || $viewer->hasAnyRole(['super_admin', 'academy_admin', 'content_editor']);
        abort_unless($privileged || $material->status === 'published', 403);

        return $this->storedAsset('public', $material->file_path, $material->original_filename, ['learning-materials/']);
    }

    private function assessmentSubmission(User $viewer, string $record, string $file): array
    {
        abort_unless(ctype_digit($record), 404);
        $session = LearningSession::findOrFail((int) $record);
        $asset = app(AssessmentSubmissionService::class)->fileForViewer($viewer, $session, $file);

        return $this->storedAsset('local', $asset['path'], $asset['name'], ['private/assessment-submissions/'.$session->id.'/']);
    }

    private function supportAttachment(User $viewer, string $record): array
    {
        abort_unless(ctype_digit($record), 404);
        $ticket = SupportTicket::findOrFail((int) $record);
        abort_unless((int) $ticket->user_id === (int) $viewer->id || $viewer->hasAnyRole(['super_admin', 'academy_admin']), 403);
        $disk = str_starts_with((string) $ticket->attachment, 'private/') ? 'local' : 'public';

        return $this->storedAsset($disk, $ticket->attachment, basename((string) $ticket->attachment), ['private/support-ticket-attachments/', 'support_ticket_attachments/']);
    }

    private function feedbackAttachment(User $viewer, string $record): array
    {
        abort_unless(ctype_digit($record), 404);
        $feedback = Feedback::findOrFail((int) $record);
        abort_unless((int) $feedback->user_id === (int) $viewer->id || $viewer->hasAnyRole(['super_admin', 'academy_admin']), 403);
        $disk = str_starts_with((string) $feedback->attachment, 'private/') ? 'local' : 'public';

        return $this->storedAsset($disk, $feedback->attachment, basename((string) $feedback->attachment), ['private/feedback-attachments/', 'feedback_attachments/']);
    }

    private function authorizeCourse(User $viewer, Course $course): void
    {
        if ($viewer->hasAnyRole(['super_admin', 'academy_admin']) || (int) $course->instructor_id === (int) $viewer->id) {
            return;
        }

        abort_unless(
            $viewer->enrollments()->where('course_id', $course->id)->exists()
            || $viewer->courses()->where('courses.id', $course->id)->exists(),
            403
        );
    }

    private function storedAsset(string $disk, mixed $path, mixed $name, array $prefixes): array
    {
        abort_unless(is_string($path) && $path !== '' && !str_contains($path, "\0") && !str_contains($path, '..') && !str_starts_with($path, '/'), 404);
        abort_unless(collect($prefixes)->contains(fn (string $prefix) => str_starts_with($path, $prefix)), 404);
        abort_unless(Storage::disk($disk)->exists($path), 404, 'The requested file is unavailable.');
        $safeName = basename(str_replace('\\', '/', is_string($name) && $name !== '' ? $name : $path));
        $safeName = preg_replace('/[\x00-\x1F\x7F]/u', '', $safeName) ?: 'download';

        return [$disk, $path, $safeName];
    }

    private function inspect(string $source, string $record, string $file, string $disk, string $path, string $name): FilePreview
    {
        $absolute = Storage::disk($disk)->path($path);
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION) ?: pathinfo($path, PATHINFO_EXTENSION));
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($absolute) ?: 'application/octet-stream';
        $size = (int) Storage::disk($disk)->size($path);
        $kind = self::PREVIEW_KINDS[$extension] ?? 'unsupported';
        $reason = self::UNSUPPORTED_REASONS[$extension] ?? null;
        $previewable = array_key_exists($extension, self::PREVIEW_KINDS);

        if (!isset(self::MIME_TYPES[$extension]) || !in_array($mime, self::MIME_TYPES[$extension], true)) {
            $previewable = false;
            $reason = 'The stored file contents do not match the recorded file type, so BootKode will not open it in the browser.';
        } elseif (in_array($extension, ['docx', 'pptx', 'xlsx'], true) && !$this->validOpenXml($absolute, $extension)) {
            $previewable = false;
            $reason = 'This Office file is incomplete or does not match its recorded format.';
        } elseif ($size <= 0) {
            $previewable = false;
            $reason = 'This file is empty and cannot be previewed.';
        } elseif (in_array($kind, ['document', 'presentation', 'spreadsheet'], true) && $size > 100 * 1024 * 1024) {
            $previewable = false;
            $reason = 'This file is too large for a safe browser preview. Download the original file instead.';
        }

        $reason ??= $previewable ? null : 'BootKode does not have a reliable in-browser renderer for this file format.';

        return new FilePreview($source, $record, $file, $disk, $path, $name, $extension, $mime, $size, $kind, $previewable, $reason);
    }

    private function validOpenXml(string $path, string $extension): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::RDONLY) !== true) {
            return false;
        }

        $required = match ($extension) {
            'docx' => 'word/document.xml', 'pptx' => 'ppt/presentation.xml', 'xlsx' => 'xl/workbook.xml',
        };
        $valid = $zip->locateName('[Content_Types].xml') !== false && $zip->locateName($required) !== false;
        $zip->close();

        return $valid;
    }
}
