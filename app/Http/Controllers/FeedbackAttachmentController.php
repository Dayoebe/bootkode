<?php

namespace App\Http\Controllers;

use App\Models\Community\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedbackAttachmentController extends Controller
{
    public function __invoke(Request $request, Feedback $feedback)
    {
        abort_unless((int) $feedback->user_id === (int) $request->user()->id
            || $request->user()->hasAnyRole(['super_admin', 'academy_admin']), 403);
        $path = $feedback->attachment;
        abort_unless(is_string($path) && !str_contains($path, '..')
            && (str_starts_with($path, 'private/feedback-attachments/') || str_starts_with($path, 'feedback_attachments/')), 404);
        $disk = str_starts_with($path, 'private/') ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);
        return Storage::disk($disk)->download($path, basename($path), ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
