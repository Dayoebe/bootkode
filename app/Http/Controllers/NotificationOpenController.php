<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationOpenController extends Controller
{
    public function __invoke(Request $request, string $notification): RedirectResponse
    {
        $user = $request->user();
        $databaseNotification = $user->notifications()->findOrFail($notification);
        $targetUrl = $this->resolveTargetUrl($request, $databaseNotification->data['action_url'] ?? null);

        $databaseNotification->delete();

        $user->logCustomActivity('Opened notification', [
            'notification_id' => $notification,
            'target_url' => $targetUrl,
        ]);

        return redirect()->to($targetUrl);
    }

    private function resolveTargetUrl(Request $request, ?string $targetUrl): string
    {
        $fallbackUrl = route('notifications');
        $targetUrl = trim((string) $targetUrl);

        if ($targetUrl === '' || $targetUrl === '#') {
            return $fallbackUrl;
        }

        if (str_starts_with($targetUrl, '/')) {
            return url($targetUrl);
        }

        if (! filter_var($targetUrl, FILTER_VALIDATE_URL)) {
            return $fallbackUrl;
        }

        $allowedHosts = array_filter([
            $request->getHost(),
            parse_url((string) config('app.url'), PHP_URL_HOST),
        ]);

        $targetHost = parse_url($targetUrl, PHP_URL_HOST);

        return $targetHost && in_array($targetHost, $allowedHosts, true)
            ? $targetUrl
            : $fallbackUrl;
    }
}
