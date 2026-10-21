<?php

namespace App\Http\Controllers;

use App\Models\Career\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobApplicationResumeController extends Controller
{
    public function __invoke(Request $request, JobApplication $jobApplication)
    {
        abort_unless((int) $request->user()->id === (int) $jobApplication->user_id
            || $request->user()->hasAnyRole(['super_admin', 'academy_admin']), 403);
        $path = $jobApplication->resume_path;
        abort_unless($path, 404);
        // New uploads are private; old paths retain compatibility behind this gate.
        $disk = str_starts_with($path, 'private/job-resumes/') ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->download($path, 'Resume.'.pathinfo($path, PATHINFO_EXTENSION), [
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
