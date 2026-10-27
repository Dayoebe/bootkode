<?php

namespace App\Http\Controllers;

use App\Models\Learning\ReviewReminder;
use App\Services\ReviewReminderService;
use Illuminate\Http\Request;

class ReviewReminderSubscriptionController extends Controller
{
    public function show(Request $request, ReviewReminder $reminder)
    {
        $this->authorizeLink($request, $reminder);

        return $this->page('review-reminders.unsubscribed', ['reminder' => $reminder, 'course' => $reminder->course, 'confirmed' => false]);
    }

    public function unsubscribe(Request $request, ReviewReminder $reminder, ReviewReminderService $service)
    {
        $this->authorizeLink($request, $reminder);
        $service->unsubscribeFromCourse($reminder);

        return $this->page('review-reminders.unsubscribed', ['reminder' => $reminder->fresh(), 'course' => $reminder->course, 'confirmed' => true]);
    }

    public function showAll()
    {
        return $this->page('review-reminders.unsubscribed-all', ['confirmed' => false]);
    }

    public function unsubscribeAll(Request $request, ReviewReminderService $service)
    {
        $service->unsubscribeFromAll($request->user());

        return $this->page('review-reminders.unsubscribed-all', ['confirmed' => true]);
    }

    private function page(string $view, array $data)
    {
        return response()->view($view, $data)->header('Cache-Control', 'private, no-store')->header('X-Robots-Tag', 'noindex, nofollow');
    }

    private function authorizeLink(Request $request, ReviewReminder $reminder): void
    {
        $expires = $request->query('expires');
        $token = $request->query('token');
        abort_unless(is_string($expires) && ctype_digit($expires) && strlen($expires) <= 12 && (int) $expires >= now()->timestamp
            && is_string($token) && strlen($token) === 64 && $request->hasValidSignature()
            && hash_equals(app(ReviewReminderService::class)->unsubscribeToken($reminder->id, (int) $expires), $token), 403);
    }
}
