<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\Question;
use App\Models\Core\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;

class AssessmentSubmissionService
{
    public const TYPES = ['project_criteria', 'assignment_question'];
    private const TEXT_MIMES = ['text/plain', 'text/markdown', 'text/csv', 'application/csv', 'text/x-php', 'text/x-python', 'text/x-script.python', 'text/html', 'text/css', 'text/javascript', 'application/javascript', 'application/json', 'application/xml', 'text/xml', 'application/x-empty'];
    private const FILE_MIMES = [
        'pdf' => ['application/pdf'], 'zip' => ['application/zip', 'application/x-zip-compressed'],
        'doc' => ['application/msword', 'application/vnd.ms-office', 'application/x-ole-storage'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
        'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'png' => ['image/png'], 'webp' => ['image/webp'],
    ];
    private const TEXT_EXTENSIONS = ['txt', 'md', 'csv', 'json', 'html', 'css', 'js', 'ts', 'php', 'py', 'sql', 'xml'];

    public function extensions(): array
    {
        return array_merge(array_keys(self::FILE_MIMES), self::TEXT_EXTENSIONS);
    }

    public function normalizeExtensions(array $extensions): array
    {
        return array_values(array_unique(array_map(fn ($value) => is_string($value) ? strtolower(ltrim(trim($value), '.')) : $value, $extensions)));
    }

    public function serverLimitKb(): int
    {
        $limits = [];
        foreach (FileUploadConfiguration::rules() as $rule) {
            if (is_string($rule) && preg_match('/^max:([0-9]+)$/', $rule, $match)) $limits[] = (int) $match[1];
        }
        foreach (['upload_max_filesize', 'post_max_size'] as $setting) {
            $value = trim((string) ini_get($setting));
            if (preg_match('/^([0-9]+)([KMG]?)$/i', $value, $match) && (int) $match[1] > 0) {
                $bytes = (int) $match[1] * (1024 ** (['' => 0, 'K' => 1, 'M' => 2, 'G' => 3][strtoupper($match[2])]));
                $limits[] = max(1, (int) floor(($bytes - ($setting === 'post_max_size' ? 65536 : 0)) / 1024));
            }
        }
        return $limits ? min($limits) : 102400;
    }

    /** Configuration is snapshotted privately; only explicit learner instructions/limits are projected. */
    public function definition(Question $question): array
    {
        $criteria = app(StructuredQuestionService::class)->array($question->options);
        $project = $question->question_type === 'project_criteria';
        if ($project) abort_unless(in_array($criteria['criteria_type'] ?? null, ['deliverable', 'rubric', 'presentation', 'documentation'], true), 422, 'Your instructor must configure this project criterion before the attempt can start.');
        $enabled = $project ? $criteria['criteria_type'] === 'deliverable' : in_array($criteria['allow_file_upload'] ?? false, [true, 1, '1'], true);
        $configured = $criteria['file_types'] ?? [];
        abort_unless(is_array($configured) && collect($configured)->every(fn ($value) => is_string($value)), 422, 'The configured file types need instructor setup.');
        $extensions = $this->normalizeExtensions($configured);
        if (!$extensions) $extensions = $this->extensions();
        $min = $project ? ($criteria['min_files'] ?? 1) : 0;
        $max = $project ? ($criteria['max_files'] ?? 5) : 1;
        $size = $criteria['max_file_size'] ?? 10;
        if ($enabled) {
            abort_unless(is_array($extensions) && !array_diff($extensions, $this->extensions()) && is_numeric($size) && $size >= 1 && $size <= 100
                && filter_var($min, FILTER_VALIDATE_INT) !== false && filter_var($max, FILTER_VALIDATE_INT) !== false && $min >= 0 && $max >= 1 && $max <= 20 && $min <= $max,
                422, 'Your instructor must correct the file requirements before this assessment can start.');
        }
        $rules = ['version' => 1, 'enabled' => $enabled, 'required' => (bool) $question->is_required,
            'extensions' => $enabled ? $extensions : [], 'max_kb' => $enabled ? min((int) $size * 1024, $this->serverLimitKb()) : 0,
            'min_files' => $enabled ? (int) $min : 0, 'max_files' => $enabled ? (int) $max : 0,
            'word_limit' => !$project && is_numeric($criteria['word_limit'] ?? null) ? (int) $criteria['word_limit'] : null];
        return ['criteria' => $criteria, 'submission_rules' => $rules, 'instructions' => strip_tags($question->explanation ?? '')];
    }

    public function validateResponse(array $grading, $value, array $files, bool $final = false, bool $expired = false): void
    {
        $rules = $grading['submission_rules'] ?? null;
        if (!$rules) return; // Legacy attempts keep their text-only contract; no new upload permissions are inferred.
        if (is_string($value) && $rules['word_limit']) {
            $words = preg_split('/\s+/u', trim($value), -1, PREG_SPLIT_NO_EMPTY);
            abort_unless(is_array($words) && count($words) <= $rules['word_limit'], 422, 'This response exceeds the recorded word limit of '.$rules['word_limit'].'.');
        }
        if (!$final || $expired) return;
        $provided = (is_string($value) && trim($value) !== '') || count($files) > 0;
        abort_if($rules['required'] && !$provided, 422, 'Complete every required project or assignment response before submitting.');
        if ($rules['enabled'] && ($rules['required'] || $provided)) {
            abort_unless(count($files) >= $rules['min_files'], 422, 'Attach the required number of project files before submitting.');
        }
        foreach ($files as $file) abort_unless($this->fileAvailable($file), 422, 'An attached file is unavailable. Remove and attach it again before submitting.');
    }

    private function active(User $user, Assessment $assessment, int $sessionId): LearningSession
    {
        User::whereKey($user->id)->lockForUpdate()->firstOrFail();
        app(CbtAttemptService::class)->authorize($assessment, $user);
        if ($assessment->lesson) app(CbtAttemptService::class)->authorizeLesson($user, $assessment->lesson);
        $session = LearningSession::where('user_id', $user->id)->where('activity_type', 'assessment')->whereNull('ended_at')->lockForUpdate()->findOrFail($sessionId);
        abort_unless((int) ($session->metadata['assessment_id'] ?? 0) === (int) $assessment->id, 403);
        abort_if($session->started_at->diffInSeconds(now(), false) > $session->metadata['duration_seconds'] + 5, 422, 'The file submission deadline has passed.');
        return $session;
    }

    public function attach(User $user, Assessment $assessment, int $sessionId, int $questionId, UploadedFile $upload): array
    {
        $stored = null;
        try {
            return DB::transaction(function () use ($user, $assessment, $sessionId, $questionId, $upload, &$stored) {
                $session = $this->active($user, $assessment, $sessionId);
                $metadata = $session->metadata;
                abort_unless(collect($metadata['questions'] ?? [])->contains('id', $questionId), 404);
                $grading = $metadata['grading'][$questionId] ?? [];
                $rules = $grading['submission_rules'] ?? [];
                abort_unless(in_array($grading['question_type'] ?? '', self::TYPES, true) && ($rules['enabled'] ?? false), 422, 'File uploads are not configured for this recorded question.');
                $files = $metadata['submission_files'][$questionId] ?? [];
                $name = basename(str_replace('\\', '/', $upload->getClientOriginalName()));
                $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name);
                abort_unless(is_string($name) && $name !== '' && mb_strlen($name) <= 180, 422, 'Use a filename of up to 180 characters.');
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                validator(['file' => $upload], ['file' => 'required|file|max:'.min($rules['max_kb'], $this->serverLimitKb())])->validate();
                if ($upload->getSize() <= 0) throw ValidationException::withMessages(['file' => 'Choose a file containing your submitted work.']);
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($upload->getRealPath());
                if (!in_array($extension, $rules['extensions'], true) || !in_array($mime, self::FILE_MIMES[$extension] ?? (in_array($extension, self::TEXT_EXTENSIONS, true) ? self::TEXT_MIMES : []), true)) {
                    throw ValidationException::withMessages(['file' => 'Choose a permitted file type whose contents match its extension.']);
                }
                $hash = hash_file('sha256', $upload->getRealPath());
                foreach ($files as $file) {
                    if ($file['sha256'] !== $hash || $file['name'] !== $name) continue;
                    abort_unless($this->fileAvailable($file), 422, 'Remove the unavailable draft file before attaching it again.');
                    return $file;
                }
                abort_unless(count($files) < $rules['max_files'], 422, 'The recorded maximum number of files has been reached.');
                $id = (string) Str::uuid();
                $path = $upload->storeAs('private/assessment-submissions/'.$session->id.'/'.$questionId, $id.'.'.$extension, ['disk' => 'local', 'visibility' => 'private']);
                if (!$path) throw new \RuntimeException('The file could not be stored. No attachment was recorded.');
                $stored = $path;
                $file = ['id' => $id, 'path' => $path, 'name' => $name, 'size' => $upload->getSize(), 'sha256' => $hash, 'uploaded_at' => now()->toISOString()];
                $metadata['submission_files'][$questionId][] = $file;
                $session->update(['metadata' => $metadata]);
                return $file;
            });
        } catch (\Throwable $exception) {
            if ($stored) Storage::disk('local')->delete($stored);
            throw $exception;
        }
    }

    public function remove(User $user, Assessment $assessment, int $sessionId, int $questionId, string $fileId): void
    {
        $path = DB::transaction(function () use ($user, $assessment, $sessionId, $questionId, $fileId) {
            $session = $this->active($user, $assessment, $sessionId);
            $metadata = $session->metadata;
            $files = $metadata['submission_files'][$questionId] ?? [];
            $file = collect($files)->firstWhere('id', $fileId);
            abort_unless($file, 404);
            $metadata['submission_files'][$questionId] = array_values(array_filter($files, fn ($item) => $item['id'] !== $fileId));
            $session->update(['metadata' => $metadata]);
            return $file['path'];
        });
        Storage::disk('local')->delete($path);
    }

    public function projectedFiles(LearningSession $session, ?int $questionId = null): array
    {
        abort_unless(auth()->user(), 403);
        $this->authorizeRead(auth()->user(), $session);
        $groups = $session->metadata['submission_files'] ?? [];
        $result = [];
        foreach ($groups as $id => $files) {
            if ($questionId !== null && (int) $id !== $questionId) continue;
            $result[$id] = array_map(fn ($file) => ['id' => $file['id'], 'name' => $file['name'], 'size' => $file['size'],
                'available' => $this->fileAvailable($file),
                'url' => route('assessment.submission-file', [$session->id, $file['id']])], $files);
        }
        return $questionId !== null ? ($result[$questionId] ?? []) : $result;
    }

    public function download(User $viewer, LearningSession $session, string $fileId)
    {
        $this->authorizeRead($viewer, $session);
        $file = collect($session->metadata['submission_files'] ?? [])->flatten(1)->firstWhere('id', $fileId);
        abort_unless($file && is_string($file['path']) && str_starts_with($file['path'], 'private/assessment-submissions/'.$session->id.'/')
            && $this->fileAvailable($file), 404, 'The recorded file is unavailable.');
        return Storage::disk('local')->download($file['path'], $file['name'], ['Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff', 'Content-Security-Policy' => 'sandbox']);
    }

    private function authorizeRead(User $viewer, LearningSession $session): void
    {
        abort_unless($session->activity_type === 'assessment', 404);
        if ((int) $session->user_id !== (int) $viewer->id) {
            abort_unless($session->ended_at, 403, 'Draft submissions are private to the learner.');
            app(AssessmentReviewService::class)->authorize($viewer, Assessment::findOrFail($session->metadata['assessment_id'] ?? 0));
        }
    }

    private function fileAvailable(array $file): bool
    {
        $path = $file['path'] ?? null;
        if (!is_string($path) || !str_starts_with($path, 'private/assessment-submissions/') || str_contains($path, '..')
            || !is_string($file['sha256'] ?? null) || !Storage::disk('local')->exists($path)) return false;
        $hash = @hash_file('sha256', Storage::disk('local')->path($path));
        return is_string($hash) && hash_equals($file['sha256'], $hash);
    }
}
