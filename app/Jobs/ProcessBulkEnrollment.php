<?php

namespace App\Jobs;

use App\Models\Admin\BulkEnrollmentBatch;
use App\Models\Admin\InstitutionUser;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Notifications\BulkEnrollmentCompleted;
use App\Services\InstitutionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ProcessBulkEnrollment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public bool $failOnTimeout = true;

    public $tries = 3;

    protected BulkEnrollmentBatch $batch;

    public function __construct(BulkEnrollmentBatch $batch)
    {
        $this->batch = $batch;
        $this->onConnection('bulk-enrollment')->onQueue('bulk-enrollment')->afterCommit();
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping('bulk-enrollment-'.$this->batch->id))->releaseAfter(30)->expireAfter(900)];
    }

    public function backoff(): array
    {
        return [60, 180];
    }

    public function handle(): void
    {
        $this->batch->refresh();
        if (in_array($this->batch->status, ['completed', 'cancelled'], true)) {
            return;
        }
        $editor = $this->batch->creator;
        if (! $editor || ! $editor->is_active || ! $editor->hasVerifiedEmail() || ! $editor->hasAnyRole(['super_admin', 'academy_admin'])) {
            throw new \RuntimeException('The original bulk enrollment administrator is no longer authorised.');
        }
        Log::info('Starting bulk enrollment processing', ['batch_id' => $this->batch->id]);

        try {
            $this->batch->start();

            $results = $this->processCsvFile();

            if (! $results['complete']) {
                self::dispatch($this->batch->fresh());

                return;
            }

            $this->batch->updateProgress(
                $results['processed'],
                $results['successful'],
                $results['failed']
            );

            $this->batch->complete();

            // Send completion notification
            $this->sendCompletionNotification($results);

            Log::info('Bulk enrollment completed successfully', [
                'batch_id' => $this->batch->id,
                'processed' => $results['processed'],
                'successful' => $results['successful'],
                'failed' => $results['failed'],
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk enrollment failed', [
                'batch_id' => $this->batch->id,
                'exception_type' => $e::class,
            ]);

            if ($this->batch->fresh()->status !== 'cancelled') {
                $this->batch->fail('Bulk enrollment stopped. Saved rows are retained; retry to resume.');
            }
            throw $e;
        }
    }

    protected function processCsvFile(): array
    {
        $filePath = str_starts_with($this->batch->file_path, DIRECTORY_SEPARATOR)
            ? $this->batch->file_path
            : Storage::disk('local')->path($this->batch->file_path);

        if (! file_exists($filePath)) {
            throw new \Exception('CSV file not found');
        }

        $fileHash = hash_file('sha256', $filePath);
        $savedHash = $this->batch->settings['resume_file_sha256'] ?? null;
        if ($savedHash && ! hash_equals($savedHash, $fileHash)) {
            throw new \RuntimeException('The CSV changed after processing started. Upload it as a new batch.');
        }
        $this->batch->update(['settings' => array_merge($this->batch->settings ?? [], ['resume_file_sha256' => $fileHash])]);

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            throw new \Exception('Could not open CSV file');
        }

        $results = [
            'processed' => (int) ($this->batch->settings['resume_row'] ?? 0),
            'successful' => (int) ($this->batch->settings['resume_successful'] ?? 0),
            'failed' => (int) ($this->batch->settings['resume_failed'] ?? 0),
            'complete' => true,
        ];

        try {
            // Read header row
            $header = fgetcsv($handle);
            if (! $header) {
                throw new \Exception('CSV file appears to be empty');
            }

            $header = array_map('trim', $header);
            $this->validateCsvHeaders($header);

            // Get course IDs to enroll users in
            $courseIds = $this->batch->courses ?? [];
            $courses = Course::publiclyAvailable()->whereIn('id', $courseIds)->get()->keyBy('id');
            if (count(array_unique($courseIds)) !== $courses->count()) {
                throw new \DomainException('Selected courses are no longer available for enrollment.');
            }

            $rowNumber = 0;
            $sliceStarted = microtime(true);
            $sliceRows = 0;
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                if ($rowNumber <= $results['processed']) {
                    continue;
                }
                DB::transaction(function () use ($row, $header, $courses, $rowNumber, &$results) {
                    $this->batch = BulkEnrollmentBatch::query()->lockForUpdate()->findOrFail($this->batch->id);
                    if ($this->batch->status === 'cancelled') {
                        throw new \RuntimeException('Bulk enrollment was cancelled.');
                    }
                    try {
                        if (count($header) !== count($row)) {
                            throw new \RuntimeException('CSV column count is invalid.');
                        }
                        $this->processUserEnrollment(array_combine($header, array_map('trim', $row)), $courses);
                        $results['successful']++;
                    } catch (\Exception $exception) {
                        $results['failed']++;
                        $this->batch->addError('Row could not be enrolled. Check the CSV values and institution capacity.', $rowNumber);
                        Log::warning('Bulk enrollment row failed', ['batch_id' => $this->batch->id, 'row' => $rowNumber, 'exception_type' => $exception::class]);
                    }
                    $results['processed'] = $rowNumber;
                    $this->batch->update([
                        'settings' => array_merge($this->batch->settings ?? [], [
                            'resume_row' => $rowNumber,
                            'resume_successful' => $results['successful'],
                            'resume_failed' => $results['failed'],
                        ]),
                        'processed_records' => $rowNumber,
                        'successful_enrollments' => $results['successful'],
                        'failed_enrollments' => $results['failed'],
                    ]);
                });
                $sliceRows++;
                if ($sliceRows >= 100 || microtime(true) - $sliceStarted >= 40) {
                    $results['complete'] = false;
                    break;
                }
            }

        } finally {
            fclose($handle);
        }

        return $results;
    }

    protected function validateCsvHeaders(array $header): void
    {
        $requiredHeaders = ['name', 'email'];
        $missingHeaders = array_diff($requiredHeaders, $header);

        if (! empty($missingHeaders)) {
            throw new \Exception('Missing required CSV headers: '.implode(', ', $missingHeaders));
        }
    }

    protected function processUserEnrollment(array $userData, $courses): void
    {
        if (empty($userData['email']) || ! filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid or missing email address');
        }

        DB::transaction(function () use ($userData, $courses) {
            // Find or create user
            $user = User::where('email', $userData['email'])->first();

            if (! $user) {
                $user = User::create([
                    'name' => $userData['name'] ?? explode('@', $userData['email'])[0],
                    'email' => $userData['email'],
                    'password' => bcrypt(\Str::random(16)), // Temporary password
                    'role' => User::ROLE_STUDENT,
                    'phone_number' => $userData['phone'] ?? null,
                    'metadata' => array_filter([
                        'institution_department' => $userData['department'] ?? null,
                    ]),
                ]);

                // Send welcome email
                $user->sendEmailVerificationNotification();
            }

            // Add user to institution if not already added
            $existingMembership = InstitutionUser::withTrashed()
                ->where('institution_id', $this->batch->institution_id)
                ->where('user_id', $user->id)
                ->first();

            if (! $existingMembership || $existingMembership->trashed()) {
                $institution = $this->batch->institution()->first();
                $institution->updateUserCount();
                $institution->ensureCanAddUsers();
            }

            $institutionUser = InstitutionUser::withTrashed()->updateOrCreate(
                ['institution_id' => $this->batch->institution_id, 'user_id' => $user->id],
                [
                    'role' => $userData['institution_role'] ?? 'student',
                    'department' => $userData['department'] ?? null,
                    'employee_id' => $userData['employee_id'] ?? null,
                    'status' => 'active',
                    'added_by' => $this->batch->created_by,
                ]
            );

            if ($institutionUser->trashed()) {
                $institutionUser->restore();
            }

            // Enroll user in courses
            foreach ($courses as $course) {
                $enrollment = CourseEnrollment::firstOrCreate(
                    ['user_id' => $user->id, 'course_id' => $course->id],
                    [
                        'enrolled_at' => now(),
                        'progress_percentage' => 0,
                        'bulk_batch_id' => $this->batch->id,
                        'enrollment_type' => 'institution',
                    ]
                );

                if (! $enrollment->wasRecentlyCreated && ! $enrollment->bulk_batch_id) {
                    $enrollment->update([
                        'bulk_batch_id' => $this->batch->id,
                        'enrollment_type' => $enrollment->enrollment_type ?: 'institution',
                    ]);
                }

                app(InstitutionService::class)->syncLegacyCourseEnrollment($user->id, $course->id);
            }
        });
    }

    protected function sendCompletionNotification(array $results): void
    {
        try {
            $creator = $this->batch->creator;
            if ($creator) {
                Notification::send($creator, new BulkEnrollmentCompleted($this->batch, $results));
            }

            // Also notify institution admin
            $institutionAdmin = $this->batch->institution->adminUser;
            if ($institutionAdmin && $institutionAdmin->id !== $creator?->id) {
                Notification::send($institutionAdmin, new BulkEnrollmentCompleted($this->batch, $results));
            }

        } catch (\Exception $e) {
            Log::warning('Failed to send bulk enrollment completion notification', [
                'batch_id' => $this->batch->id,
                'exception_type' => $e::class,
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->batch->refresh();
        if (in_array($this->batch->status, ['completed', 'cancelled'], true)) {
            return;
        }
        Log::error('Bulk enrollment job failed', [
            'batch_id' => $this->batch->id,
            'exception_type' => $exception::class,
        ]);

        try {
            $this->batch->fail('Job failed after retry. Saved rows are retained for recovery.');
        } catch (\Exception $e) {
            Log::error('Failed to update batch status after job failure', [
                'batch_id' => $this->batch->id,
                'exception_type' => $e::class,
            ]);
        }
    }
}
