<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BulkEnrollmentBatch;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\InstitutionUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BulkEnrollmentCompleted;

class ProcessBulkEnrollment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout
    public $tries = 3;

    protected BulkEnrollmentBatch $batch;

    public function __construct(BulkEnrollmentBatch $batch)
    {
        $this->batch = $batch;
    }

    public function handle(): void
    {
        Log::info('Starting bulk enrollment processing', ['batch_id' => $this->batch->id]);

        try {
            $this->batch->start();
            
            $results = $this->processCsvFile();
            
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
                'failed' => $results['failed']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bulk enrollment failed', [
                'batch_id' => $this->batch->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->batch->fail($e->getMessage());
            throw $e;
        }
    }

    protected function processCsvFile(): array
    {
        $filePath = storage_path('app/' . $this->batch->file_path);
        
        if (!file_exists($filePath)) {
            throw new \Exception('CSV file not found');
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not open CSV file');
        }

        $results = [
            'processed' => 0,
            'successful' => 0,
            'failed' => 0
        ];

        try {
            // Read header row
            $header = fgetcsv($handle);
            if (!$header) {
                throw new \Exception('CSV file appears to be empty');
            }

            $header = array_map('trim', $header);
            $this->validateCsvHeaders($header);

            // Get course IDs to enroll users in
            $courseIds = $this->batch->courses ?? [];
            $courses = Course::whereIn('id', $courseIds)->get()->keyBy('id');

            // Process each row
            while (($row = fgetcsv($handle)) !== false) {
                $results['processed']++;
                
                try {
                    $userData = array_combine($header, array_map('trim', $row));
                    $this->processUserEnrollment($userData, $courses);
                    $results['successful']++;
                    
                } catch (\Exception $e) {
                    $results['failed']++;
                    $this->batch->addError(
                        $e->getMessage(),
                        $results['processed']
                    );
                    
                    Log::warning('Failed to process enrollment row', [
                        'batch_id' => $this->batch->id,
                        'row' => $results['processed'],
                        'error' => $e->getMessage(),
                        'data' => $userData ?? 'N/A'
                    ]);
                }

                // Update progress every 100 records
                if ($results['processed'] % 100 === 0) {
                    $this->batch->updateProgress(
                        $results['processed'],
                        $results['successful'],
                        $results['failed']
                    );
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
        
        if (!empty($missingHeaders)) {
            throw new \Exception('Missing required CSV headers: ' . implode(', ', $missingHeaders));
        }
    }

    protected function processUserEnrollment(array $userData, $courses): void
    {
        if (empty($userData['email']) || !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid or missing email address');
        }

        DB::transaction(function () use ($userData, $courses) {
            // Find or create user
            $user = User::where('email', $userData['email'])->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'] ?? explode('@', $userData['email'])[0],
                    'email' => $userData['email'],
                    'password' => bcrypt(\Str::random(16)), // Temporary password
                    'role' => $userData['role'] ?? User::ROLE_STUDENT,
                    'phone_number' => $userData['phone'] ?? null,
                    'department' => $userData['department'] ?? null
                ]);

                // Send welcome email
                $user->sendEmailVerificationNotification();
            }

            // Add user to institution if not already added
            InstitutionUser::updateOrCreate(
                [
                    'institution_id' => $this->batch->institution_id,
                    'user_id' => $user->id
                ],
                [
                    'role' => $userData['institution_role'] ?? 'student',
                    'department' => $userData['department'] ?? null,
                    'employee_id' => $userData['employee_id'] ?? null,
                    'status' => 'active',
                    'added_by' => $this->batch->created_by
                ]
            );

            // Enroll user in courses
            foreach ($courses as $course) {
                CourseEnrollment::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $course->id
                    ],
                    [
                        'enrolled_at' => now(),
                        'progress_percentage' => 0,
                        'bulk_batch_id' => $this->batch->id
                    ]
                );
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
            if ($institutionAdmin && $institutionAdmin->id !== $creator->id) {
                Notification::send($institutionAdmin, new BulkEnrollmentCompleted($this->batch, $results));
            }

        } catch (\Exception $e) {
            Log::warning('Failed to send bulk enrollment completion notification', [
                'batch_id' => $this->batch->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Bulk enrollment job failed', [
            'batch_id' => $this->batch->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        try {
            $this->batch->fail('Job failed: ' . $exception->getMessage());
        } catch (\Exception $e) {
            Log::error('Failed to update batch status after job failure', [
                'batch_id' => $this->batch->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}