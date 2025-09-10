<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function show($slug)
    {
        // Get the published page by slug
        $page = Page::where('slug', $slug)
                   ->published() // Make sure this scope exists in your Page model
                   ->first();
        
        // If page doesn't exist or isn't published, return 404
        if (!$page) {
            abort(404);
        }
        
        // Increment view count
        $page->incrementViewCount();
        
        // Return the view with the page data
        return view('livewire.pages.partials.show', compact('page'));
    }
    public function trackView(Request $request, $slug)
    {
        $page = Page::where('slug', $slug)->published()->first();
        
        if ($page) {
            // Increment view count
            $this->incrementViewCount($page);
            
            // You can add more detailed analytics tracking here
            // For example, track referrers, user agents, etc.
        }
        
        return response()->json(['success' => true]);
    }

    private function incrementViewCount($page)
    {
        // Use a job or dispatch after response for better performance
        dispatch(function () use ($page) {
            $page->increment('view_count');
        })->afterResponse();
    }
}
