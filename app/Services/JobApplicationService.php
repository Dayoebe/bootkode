<?php

namespace App\Services;

use App\Models\Career\JobApplication;
use App\Models\Career\JobPortal;
use App\Models\Core\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JobApplicationService
{
    public function availableJob(int $id, bool $lock = false): JobPortal
    {
        $query = JobPortal::active()->where('application_method', 'internal')
            ->where(fn ($query) => $query->whereNull('application_deadline')->orWhere('application_deadline', '>', now()));
        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->findOrFail($id);
    }

    public function submit(User $user, int $jobId, string $coverLetter, array $responses = [], ?UploadedFile $resume = null): JobApplication
    {
        $resumePath = null;
        try {
            return DB::transaction(function () use ($user, $jobId, $coverLetter, $responses, $resume, &$resumePath) {
                $job = $this->availableJob($jobId, lock: true);
                $existing = JobApplication::where('job_id', $job->id)->where('user_id', $user->id)->first();
                if ($existing) {
                    return $existing;
                }
                if ($resume) {
                    $resumePath = $resume->store('private/job-resumes/'.$user->id, 'local');
                    if (!$resumePath) {
                        throw new \RuntimeException('Resume could not be stored.');
                    }
                }
                $application = JobApplication::create([
                    'job_id' => $job->id, 'user_id' => $user->id, 'cover_letter' => $coverLetter,
                    'custom_responses' => $responses, 'resume_path' => $resumePath,
                    'status' => JobPortal::APPLICATION_PENDING,
                ]);
                $job->increment('applications_count');

                return $application;
            });
        } catch (\Throwable $exception) {
            if ($resumePath) {
                Storage::disk('local')->delete($resumePath);
            }
            throw $exception;
        }
    }
}
