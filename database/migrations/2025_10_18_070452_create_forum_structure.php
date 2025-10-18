<?php
// ============ MIGRATION: Create Forum Structure ============
// database/migrations/2025_01_18_create_forum_structure.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Categories (Politics, Romance, Education, Business, etc.)
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Subcategories
        Schema::create('forum_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('forum_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('threads_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->timestamps();

            $table->unique(['category_id', 'slug']);
        });

        // Forum Threads (Posts)
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained('forum_subcategories')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'locked'])->default('pending_approval');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->integer('views')->default(0);
            $table->integer('replies_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['subcategory_id', 'status']);
            $table->index(['status', 'is_featured']);
            $table->index('created_at');
        });

        // Forum Replies (Comments on threads)
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->longText('content');
            $table->enum('status', ['pending_approval', 'approved', 'rejected'])->default('pending_approval');
            $table->integer('likes_count')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['thread_id', 'status']);
            $table->index('created_at');
        });

        // Thread Tags
        Schema::create('forum_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });

        Schema::create('forum_thread_tags', function (Blueprint $table) {
            $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('forum_tags')->onDelete('cascade');
            $table->primary(['thread_id', 'tag_id']);
        });

        // Moderation Queue
        Schema::create('moderation_queue', function (Blueprint $table) {
            $table->id();
            $table->morphs('content'); // thread or reply
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('moderator_comment')->nullable();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // User Engagement (Likes, Bookmarks)
        Schema::create('forum_engagement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->morphs('engageable'); // thread or reply
            $table->enum('type', ['like', 'bookmark', 'report'])->default('like');
            $table->timestamps();

            $table->unique(['user_id', 'engageable_type', 'engageable_id', 'type']);
        });

        // Trending Calculation
        Schema::create('trending_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->float('score')->default(0);
            $table->integer('views_24h')->default(0);
            $table->integer('replies_24h')->default(0);
            $table->integer('likes_24h')->default(0);
            $table->timestamps();

            $table->unique('thread_id');
        });
    }

    public function down() {
        Schema::dropIfExists('trending_scores');
        Schema::dropIfExists('forum_engagement');
        Schema::dropIfExists('moderation_queue');
        Schema::dropIfExists('forum_thread_tags');
        Schema::dropIfExists('forum_tags');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_threads');
        Schema::dropIfExists('forum_subcategories');
        Schema::dropIfExists('forum_categories');
    }
};
