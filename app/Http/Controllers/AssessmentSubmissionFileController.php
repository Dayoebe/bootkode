<?php

namespace App\Http\Controllers;

use App\Models\Assessment\LearningSession;
use App\Services\AssessmentSubmissionService;
use Illuminate\Http\Request;

class AssessmentSubmissionFileController extends Controller
{
    public function __invoke(Request $request, LearningSession $session, string $file)
    {
        return app(AssessmentSubmissionService::class)->download($request->user(), $session, $file);
    }
}
