<?php

namespace App\Services;

use App\Models\Core\Institution;
use App\Models\Core\User;
use App\Models\Admin\BulkEnrollmentBatch;
use App\Models\Admin\InstitutionCohort;
use App\Models\Admin\InstitutionInvitation;
use App\Models\Learning\CourseEnrollment;
use App\Models\Admin\InstitutionUser;
use App\Models\Learning\Course;
use App\Jobs\ProcessBulkEnrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use App\Notifications\InstitutionWelcome;
use App\Notifications\InstitutionAdminInvitation;
use App\Notifications\LicenseExpiring;
use Illuminate\Support\Str;
use App\Notifications\BulkEnrollmentCompleted;

class InstitutionService
{
    /**
     * Create a new institution with admin user
     */
    public function createInstitution(array $data, $logoFile = null): Institution
    {
        return DB::transaction(function () use ($data, $logoFile) {
            // Handle logo upload
            $logoPath = null;
            if ($logoFile) {
                $logoPath = $logoFile->store('institution-logos', 'public');
            }

            // Find or create admin user
            $adminUser = User::where('email', $data['admin_email'])->first();
            if (!$adminUser) {
                $adminUser = User::create([
                    'name' => explode('@', $data['admin_email'])[0],
                    'email' => $data['admin_email'],
                    'password' => bcrypt(Str::random(16)), // Temporary password
                    'role' => User::ROLE_ACADEMY_ADMIN
                ]);
                
                // Send welcome email
                $adminUser->sendEmailVerificationNotification();
            }

            // Create institution
            $institution = Institution::create(array_merge($data, [
                'logo' => $logoPath,
                'admin_user_id' => $adminUser->id,
                'created_by' => auth()->id()
            ]));

            // Create admin user relationship
            $institution->users()->create([
                'user_id' => $adminUser->id,
                'role' => 'admin',
                'status' => 'active',
                'added_by' => auth()->id()
            ]);

            // Send welcome notification
            $this->sendWelcomeNotification($institution);

            return $institution;
        });
    }

    /**
     * Update institution information
     */
    public function updateInstitution(Institution $institution, array $data, $logoFile = null): Institution
    {
        return DB::transaction(function () use ($institution, $data, $logoFile) {
            // Handle logo upload
            if ($logoFile) {
                // Delete old logo
                if ($institution->logo) {
                    Storage::disk('public')->delete($institution->logo);
                }
                $data['logo'] = $logoFile->store('institution-logos', 'public');
            }

            // Update admin user if email changed
            if (isset($data['admin_email']) && $data['admin_email'] !== $institution->adminUser?->email) {
                $adminUser = User::where('email', $data['admin_email'])->first();
                if (!$adminUser) {
                    $adminUser = User::create([
                        'name' => explode('@', $data['admin_email'])[0],
                        'email' => $data['admin_email'],
                        'password' => bcrypt(\Str::random(16)),
                        'role' => User::ROLE_ACADEMY_ADMIN
                    ]);
                }
                
                $data['admin_user_id'] = $adminUser->id;
                
                // Update or create admin relationship
                $institution->users()->updateOrCreate(
                    ['user_id' => $adminUser->id],
                    [
                        'role' => 'admin',
                        'status' => 'active',
                        'added_by' => auth()->id()
                    ]
                );
            }

            $institution->update($data);
            return $institution->fresh();
        });
    }

    /**
     * Process bulk enrollment from CSV file
     */
    public function processBulkEnrollment(Institution $institution, $filePath, array $courses, array $settings = []): BulkEnrollmentBatch
    {
        [$absolutePath, $storedPath] = $this->normalizeEnrollmentFilePath($filePath);

        if (! file_exists($absolutePath)) {
            throw new \RuntimeException('CSV file not found.');
        }

        $institution->updateUserCount();
        $institution->ensureCanAddUsers($this->countNewInstitutionUsersFromCsv($institution, $absolutePath));

        // Parse CSV to count total records
        $handle = fopen($absolutePath, 'r');
        $totalRecords = 0;
        fgetcsv($handle);
        
        while (fgetcsv($handle)) {
            $totalRecords++;
        }
        fclose($handle);

        // Create batch record
        $batch = $institution->bulkEnrollments()->create([
            'name' => $settings['name'] ?? 'Bulk Enrollment ' . now()->format('M d, Y'),
            'description' => $settings['description'] ?? null,
            'file_path' => $storedPath,
            'original_filename' => $settings['original_filename'] ?? 'upload.csv',
            'total_records' => $totalRecords,
            'courses' => $courses,
            'settings' => $settings,
            'created_by' => auth()->id()
        ]);

        // Dispatch job to process in background
        ProcessBulkEnrollment::dispatch($batch);

        return $batch;
    }

    /**
     * Add users to institution in bulk
     */
    public function addUsersToInstitution(Institution $institution, array $userData): array
    {
        $institution->updateUserCount();
        $institution->ensureCanAddUsers($this->countNewInstitutionUsersFromRows($institution, $userData));

        $results = [
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($userData as $index => $data) {
            try {
                DB::transaction(function () use ($institution, $data, $index, &$results) {
                    // Find or create user
                    $user = User::where('email', $data['email'])->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $data['name'] ?? explode('@', $data['email'])[0],
                            'email' => $data['email'],
                            'password' => bcrypt(\Str::random(16)),
                            'role' => $data['role'] ?? User::ROLE_STUDENT
                        ]);
                    }

                    // Add to institution
                    $institution->users()->updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'role' => $data['institution_role'] ?? 'student',
                            'department' => $data['department'] ?? null,
                            'employee_id' => $data['employee_id'] ?? null,
                            'status' => 'active',
                            'added_by' => auth()->id()
                        ]
                    );

                    $results['successful']++;
                });
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $index + 1,
                    'email' => $data['email'] ?? 'Unknown',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Enroll institution users in courses
     */
    public function enrollUsersInCourses(Institution $institution, array $courseIds, array $userIds = null): array
    {
        $results = [
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        // Get users to enroll
        $users = $userIds 
            ? $institution->users()->whereIn('user_id', $userIds)->pluck('user_id')
            : $institution->users()->where('status', 'active')->pluck('user_id');

        foreach ($users as $userId) {
            foreach ($courseIds as $courseId) {
                try {
                    $enrollment = CourseEnrollment::firstOrCreate(
                        ['user_id' => $userId, 'course_id' => $courseId],
                        ['enrolled_at' => now(), 'progress_percentage' => 0, 'enrollment_type' => 'institution']
                    );

                    if (! $enrollment->wasRecentlyCreated && ! $enrollment->enrollment_type) {
                        $enrollment->update(['enrollment_type' => 'institution']);
                    }

                    $this->syncLegacyCourseEnrollment((int) $userId, (int) $courseId);

                    $results['successful']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'error' => $e->getMessage()
                    ];
                }
            }
        }

        return $results;
    }

    public function inviteInstitutionAdmin(Institution $institution, array $data): InstitutionInvitation
    {
        $role = $data['role'] ?? 'admin';
        $email = strtolower(trim($data['email']));
        $name = trim($data['name'] ?? '') ?: Str::before($email, '@');

        return DB::transaction(function () use ($institution, $data, $role, $email, $name) {
            $user = User::where('email', $email)->first();
            $existingMember = $user
                ? $institution->users()->where('user_id', $user->id)->withTrashed()->first()
                : null;

            if (! $existingMember || $existingMember->trashed()) {
                $institution->updateUserCount();
                $institution->ensureCanAddUsers();
            }

            $userRole = $this->mapInstitutionRoleToUserRole($role);

            if (! $user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(Str::random(32)),
                    'role' => $userRole,
                    'email_verified_at' => now(),
                ]);
            } elseif ($this->shouldPromoteUserForInstitutionRole($user, $userRole)) {
                $user->update(['role' => $userRole]);
            }

            $membershipIsAlreadyActive = $existingMember && ! $existingMember->trashed() && $existingMember->status === 'active';

            $institutionUser = $institution->users()->withTrashed()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role' => $role,
                    'department' => $data['department'] ?? null,
                    'status' => $membershipIsAlreadyActive ? 'active' : 'pending',
                    'joined_at' => $membershipIsAlreadyActive ? ($existingMember->joined_at ?? now()) : null,
                    'left_at' => null,
                    'added_by' => auth()->id(),
                ]
            );

            if ($institutionUser->trashed()) {
                $institutionUser->restore();
            }

            $invitation = InstitutionInvitation::create([
                'institution_id' => $institution->id,
                'user_id' => $user->id,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'department' => $data['department'] ?? null,
                'invited_by' => auth()->id(),
                'status' => $membershipIsAlreadyActive ? 'accepted' : 'pending',
                'accepted_at' => $membershipIsAlreadyActive ? now() : null,
                'accepted_user_id' => $membershipIsAlreadyActive ? $user->id : null,
                'expires_at' => now()->addDays((int) ($data['expires_in_days'] ?? 14)),
            ]);

            if (! $membershipIsAlreadyActive) {
                Notification::send($user, new InstitutionAdminInvitation($invitation));
            }
            $institution->updateUserCount();

            return $invitation;
        });
    }

    public function enrollCohortInAssignedCourses(InstitutionCohort $cohort): array
    {
        $courseIds = $cohort->courses()->pluck('courses.id')->all();
        $userIds = $cohort->members()
            ->where('institution_users.status', 'active')
            ->pluck('institution_users.user_id')
            ->all();

        return $this->enrollUsersInCourses($cohort->institution, $courseIds, $userIds);
    }

    /**
     * Generate institution analytics
     */
    public function generateAnalytics(Institution $institution): array
    {
        $userIds = $institution->users()->pluck('user_id')->all();
        
        return [
            'users' => [
                'total' => $institution->users()->count(),
                'active' => $institution->users()->where('status', 'active')->count(),
                'by_role' => $institution->users()
                    ->selectRaw('role, COUNT(*) as count')
                    ->groupBy('role')
                    ->pluck('count', 'role')
                    ->toArray()
            ],
            'enrollments' => [
                'total' => CourseEnrollment::whereIn('user_id', $userIds)->count(),
                'completed' => CourseEnrollment::whereIn('user_id', $userIds)
                    ->where('is_completed', true)->count(),
                'in_progress' => CourseEnrollment::whereIn('user_id', $userIds)
                    ->where('is_completed', false)
                    ->where('progress_percentage', '>', 0)->count(),
                'average_progress' => CourseEnrollment::whereIn('user_id', $userIds)
                    ->avg('progress_percentage') ?? 0
            ],
            'certificates' => [
                'total' => \App\Models\Credentials\Certificate::whereIn('user_id', $userIds)
                    ->where('status', 'approved')->count(),
                'this_month' => \App\Models\Credentials\Certificate::whereIn('user_id', $userIds)
                    ->where('status', 'approved')
                    ->whereMonth('issued_date', now()->month)->count()
            ],
            'activity' => [
                'monthly_enrollments' => $this->getMonthlyEnrollments($userIds),
                'popular_courses' => $this->getPopularCourses($userIds),
                'completion_trends' => $this->getCompletionTrends($userIds)
            ]
        ];
    }

    /**
     * Send welcome notification to institution
     */
    public function sendWelcomeNotification(Institution $institution): void
    {
        if ($institution->adminUser) {
            Notification::send(
                $institution->adminUser,
                new InstitutionWelcome($institution)
            );
        }
    }

    /**
     * Check and notify about expiring licenses
     */
    public function checkExpiringLicenses(): void
    {
        $expiringInstitutions = Institution::where('status', 'active')
            ->whereBetween('license_end_date', [now(), now()->addDays(30)])
            ->with('adminUser')
            ->get();

        foreach ($expiringInstitutions as $institution) {
            if ($institution->adminUser) {
                Notification::send(
                    $institution->adminUser,
                    new LicenseExpiring($institution)
                );
            }
        }
    }

    /**
     * Generate institution usage report
     */
    public function generateUsageReport(Institution $institution, $startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonth();
        $endDate = $endDate ?? now();

        $userIds = $institution->users()->pluck('user_id');

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'users' => [
                'total' => $institution->users()->count(),
                'active_in_period' => User::whereIn('id', $userIds)
                    ->whereBetween('last_login_at', [$startDate, $endDate])
                    ->count()
            ],
            'courses' => [
                'total_accessed' => CourseEnrollment::whereIn('user_id', $userIds)
                    ->whereBetween('enrolled_at', [$startDate, $endDate])
                    ->distinct('course_id')
                    ->count(),
                'completed' => CourseEnrollment::whereIn('user_id', $userIds)
                    ->whereBetween('completed_at', [$startDate, $endDate])
                    ->whereNotNull('completed_at')
                    ->count()
            ],
            'engagement' => [
                'total_study_time' => \App\Models\Assessment\LearningSession::whereIn('user_id', $userIds)
                    ->whereBetween('started_at', [$startDate, $endDate])
                    ->whereNotNull('ended_at')
                    ->sum('duration_minutes'),
                'average_session_duration' => \App\Models\Assessment\LearningSession::whereIn('user_id', $userIds)
                    ->whereBetween('started_at', [$startDate, $endDate])
                    ->whereNotNull('ended_at')
                    ->avg('duration_minutes') ?? 0
            ]
        ];
    }

    /**
     * Export institution data
     */
    public function exportInstitutionData(Institution $institution): array
    {
        return [
            'institution' => $institution->toArray(),
            'users' => $institution->users()->with('user')->get()->map(function ($institutionUser) {
                return [
                    'name' => $institutionUser->user->name,
                    'email' => $institutionUser->user->email,
                    'role' => $institutionUser->role,
                    'department' => $institutionUser->department,
                    'status' => $institutionUser->status,
                    'joined_at' => $institutionUser->joined_at
                ];
            }),
            'enrollments' => CourseEnrollment::whereIn('user_id', $institution->users()->pluck('user_id'))
                ->with(['course', 'user'])
                ->get()
                ->map(function ($enrollment) {
                    return [
                        'user_name' => $enrollment->user->name,
                        'user_email' => $enrollment->user->email,
                        'course_title' => $enrollment->course->title,
                        'enrolled_at' => $enrollment->enrolled_at,
                        'progress_percentage' => $enrollment->progress_percentage,
                        'is_completed' => $enrollment->is_completed,
                        'completed_at' => $enrollment->completed_at
                    ];
                })
        ];
    }

    // Private helper methods
    private function getMonthlyEnrollments(array $userIds): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[$date->format('M Y')] = CourseEnrollment::whereIn('user_id', $userIds)
                ->whereYear('enrolled_at', $date->year)
                ->whereMonth('enrolled_at', $date->month)
                ->count();
        }
        return $months;
    }

    private function getPopularCourses(array $userIds, int $limit = 10): array
    {
        return CourseEnrollment::whereIn('user_id', $userIds)
            ->select('course_id')
            ->selectRaw('COUNT(*) as enrollment_count')
            ->with('course:id,title')
            ->groupBy('course_id')
            ->orderBy('enrollment_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'course_title' => $item->course->title,
                    'enrollment_count' => $item->enrollment_count
                ];
            })
            ->toArray();
    }

    private function getCompletionTrends(array $userIds): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[$date->format('M Y')] = CourseEnrollment::whereIn('user_id', $userIds)
                ->whereYear('completed_at', $date->year)
                ->whereMonth('completed_at', $date->month)
                ->whereNotNull('completed_at')
                ->count();
        }
        return $months;
    }

    public function syncLegacyCourseEnrollment(int $userId, int $courseId): void
    {
        if (! Schema::hasTable('course_user')) {
            return;
        }

        $now = now();
        $exists = DB::table('course_user')
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();

        if ($exists) {
            DB::table('course_user')
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->update(['updated_at' => $now]);

            return;
        }

        DB::table('course_user')->insert([
            'user_id' => $userId,
            'course_id' => $courseId,
            'progress' => 0,
            'last_accessed_at' => null,
            'time_spent_minutes' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function normalizeEnrollmentFilePath(string $filePath): array
    {
        $storageRoot = storage_path('app') . DIRECTORY_SEPARATOR;

        if (Str::startsWith($filePath, $storageRoot)) {
            return [$filePath, Str::after($filePath, $storageRoot)];
        }

        if (Str::startsWith($filePath, DIRECTORY_SEPARATOR)) {
            return [$filePath, $filePath];
        }

        return [storage_path('app/' . $filePath), $filePath];
    }

    private function countNewInstitutionUsersFromCsv(Institution $institution, string $absolutePath): int
    {
        $handle = fopen($absolutePath, 'r');
        if (! $handle) {
            throw new \RuntimeException('Could not open CSV file.');
        }

        $emails = [];

        try {
            $header = fgetcsv($handle);
            if (! $header) {
                return 0;
            }

            $header = array_map(fn ($value) => strtolower(trim($value)), $header);
            $emailIndex = array_search('email', $header, true);

            if ($emailIndex === false) {
                return 0;
            }

            while (($row = fgetcsv($handle)) !== false) {
                $email = strtolower(trim($row[$emailIndex] ?? ''));
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emails[$email] = true;
                }
            }
        } finally {
            fclose($handle);
        }

        return $this->countNewInstitutionUsersFromEmails($institution, array_keys($emails));
    }

    private function countNewInstitutionUsersFromRows(Institution $institution, array $rows): int
    {
        $emails = collect($rows)
            ->pluck('email')
            ->filter()
            ->map(fn ($email) => strtolower(trim($email)))
            ->unique()
            ->values()
            ->all();

        return $this->countNewInstitutionUsersFromEmails($institution, $emails);
    }

    private function countNewInstitutionUsersFromEmails(Institution $institution, array $emails): int
    {
        if (empty($emails)) {
            return 0;
        }

        $existingEmails = User::query()
            ->whereIn('email', $emails)
            ->whereHas('institutionMemberships', function ($query) use ($institution) {
                $query->where('institution_id', $institution->id)
                    ->whereIn('status', ['active', 'pending']);
            })
            ->pluck('email')
            ->map(fn ($email) => strtolower($email))
            ->all();

        return collect($emails)
            ->diff($existingEmails)
            ->count();
    }

    private function mapInstitutionRoleToUserRole(string $role): string
    {
        return match ($role) {
            'admin' => User::ROLE_ACADEMY_ADMIN,
            'instructor' => User::ROLE_INSTRUCTOR,
            default => User::ROLE_STUDENT,
        };
    }

    private function shouldPromoteUserForInstitutionRole(User $user, string $role): bool
    {
        if ($user->isSuperAdmin()) {
            return false;
        }

        if ($role === User::ROLE_ACADEMY_ADMIN) {
            return ! $user->isAcademyAdmin();
        }

        if ($role === User::ROLE_INSTRUCTOR) {
            return $user->isStudent();
        }

        return false;
    }
}
