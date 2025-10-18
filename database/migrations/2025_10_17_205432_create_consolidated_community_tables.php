<?php
// database/migrations/2025_10_17_000000_create_consolidated_community_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Forum Threads
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('category')->default('general'); 
            $table->string('title');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->integer('views')->default(0);
            $table->integer('replies_count')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->foreignId('last_reply_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('category');
            $table->index('created_at');
            $table->index('is_pinned');
        });

        // Forum Replies
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('thread_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        // Community Activities (Study Groups, Code Challenges, Live Events)
        Schema::create('community_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['study_group', 'code_challenge', 'live_event']);
            $table->string('title');
            $table->text('description');
            $table->json('tags')->nullable();
            $table->integer('max_participants')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('location')->nullable();
            $table->text('requirements')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->integer('participants_count')->default(0);
            $table->timestamps();
            
            $table->index('type');
            $table->index('status');
            $table->index('creator_id');
            $table->index('created_at');
        });

        // Activity Participants
        Schema::create('activity_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('community_activities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['joined', 'pending', 'completed', 'left'])->default('joined');
            $table->json('submission_data')->nullable();
            $table->integer('score')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['activity_id', 'user_id']);
            $table->index('status');
            $table->index('user_id');
        });

        // Community Feedback
        Schema::create('community_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('category')->default('general');
            $table->string('subject');
            $table->text('message');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            
            $table->index('category');
            $table->index('status');
            $table->index('priority');
            $table->index('user_id');
            $table->index('created_at');
        });

        // Community Reports
        Schema::create('community_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id');
            $table->enum('reason', ['spam', 'inappropriate', 'harassment', 'plagiarism', 'other']);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->foreignId('moderator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('moderator_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['reportable_type', 'reportable_id']);
            $table->index('status');
            $table->index('reporter_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_reports');
        Schema::dropIfExists('community_feedback');
        Schema::dropIfExists('activity_participants');
        Schema::dropIfExists('community_activities');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_threads');
    }
};