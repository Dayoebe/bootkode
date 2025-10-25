<?php
// Controller: NewsletterController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\NewsletterSubscriber;
use App\Models\Admin\NewsletterInteraction;
use App\Models\Admin\NewsletterCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsletterController extends Controller
{
    /**
     * Track email opens via pixel
     */

public function trackOpen($token)
{
    NewsletterInteraction::trackOpen($token);
    
    // Return 1x1 transparent pixel
    $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
    
    return response($pixel, 200, [
        'Content-Type' => 'image/gif',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
    ]);
}

public function trackClick($token, Request $request)
{
    $url = base64_decode($request->get('url'));
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        abort(400, 'Invalid URL');
    }

    NewsletterInteraction::trackClick(
        $token, 
        $url, 
        $request->ip(), 
        $request->userAgent()
    );

    return redirect($url);
}
    /**
     * Handle unsubscribe requests
     */
    // public function unsubscribe($token)
    // {
    //     $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();
    
    //     if (!$subscriber) {
    //         abort(404, 'Invalid unsubscribe token');
    //     }
    
    //     if ($subscriber->status !== NewsletterSubscriber::STATUS_UNSUBSCRIBED) {
    //         $subscriber->unsubscribe();
    //     }
    
    //     $settings = NewsletterCampaign::getSetting('unsubscribe_page_content', [
    //         'title' => 'Unsubscribe Confirmation',
    //         'message' => 'You have been successfully unsubscribed from our newsletter.',
    //         'resubscribe_text' => 'Changed your mind? You can resubscribe anytime.'
    //     ]);
    
    //     return view('newsletter.unsubscribe', compact('subscriber', 'settings'));
    // }

    /**
     * Handle resubscribe requests
     */
    public function resubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            abort(404, 'Invalid subscriber token');
        }

        $subscriber->resubscribe();

        return redirect()->back()->with('success', 'You have been successfully resubscribed to our newsletter.');
    }

    /**
     * Public subscription endpoint
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $request->email],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'status' => NewsletterSubscriber::STATUS_ACTIVE,
                'source' => 'website',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to newsletter.',
                'subscriber' => $subscriber
            ]);
        }

        return redirect()->back()->with('success', 'Successfully subscribed to newsletter.');
    }
/**
 * Handle subscriber preferences page
 */
public function preferences($token)
{
    $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

    if (!$subscriber) {
        abort(404, 'Invalid preferences token');
    }

    $settings = NewsletterCampaign::getSetting('preferences_page_content', [
        'title' => 'Subscription Preferences',
        'message' => 'Manage your email preferences',
    ]);

    return view('newsletter.preferences', compact('subscriber', 'settings'));
}
/**
 * Handle unsubscribe requests
 */
public function unsubscribe($token)
{
    $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

    if (!$subscriber) {
        abort(404, 'Invalid unsubscribe token');
    }

    // Only unsubscribe if not already unsubscribed
    if ($subscriber->status !== NewsletterSubscriber::STATUS_UNSUBSCRIBED) {
        $subscriber->unsubscribe();
    }

    $settings = NewsletterCampaign::getSetting('unsubscribe_page_content', [
        'title' => 'Unsubscribe Confirmation',
        'message' => 'You have been successfully unsubscribed from our newsletter.',
        'resubscribe_text' => 'Changed your mind? You can resubscribe anytime.'
    ]);

    return view('newsletter.unsubscribe', compact('subscriber', 'settings'));
}
}