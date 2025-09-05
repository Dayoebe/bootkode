<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogComment;
use App\Models\BlogReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function toggleReaction(Request $request, BlogPost $post)
    {
        $request->validate([
            'type' => 'required|in:like,bookmark'
        ]);

        $userId = auth()->id();
        $ipAddress = $request->ip();
        
        $added = BlogReaction::toggle(
            BlogPost::class,
            $post->id,
            $request->type,
            $userId,
            $userId ? null : $ipAddress
        );

        // Update counter on post
        if ($request->type === 'like') {
            $likesCount = $post->likes()->count();
            $post->update(['likes_count' => $likesCount]);
        }

        return response()->json([
            'success' => true,
            'added' => $added,
            'count' => $request->type === 'like' ? $post->likes_count : $post->bookmarks()->count()
        ]);
    }

    public function toggleCommentReaction(Request $request, BlogComment $comment)
    {
        $userId = auth()->id();
        $ipAddress = $request->ip();
        
        $added = BlogReaction::toggle(
            BlogComment::class,
            $comment->id,
            'like',
            $userId,
            $userId ? null : $ipAddress
        );

        // Update counter on comment
        $likesCount = $comment->likes()->count();
        $comment->update(['likes_count' => $likesCount]);

        return response()->json([
            'success' => true,
            'added' => $added,
            'count' => $comment->likes_count
        ]);
    }

    public function incrementView(Request $request, BlogPost $post)
    {
        // Only increment if not viewed in this session
        $sessionKey = 'blog_post_viewed_' . $post->id;
        
        if (!session()->has($sessionKey)) {
            $post->incrementViews();
            session([$sessionKey => true]);
        }

        return response()->json(['success' => true]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048' // 2MB max
        ]);

        try {
            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('blog/images', $filename, 'public');
            
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
