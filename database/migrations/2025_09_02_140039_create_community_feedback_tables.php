<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Community Feedback System
        Schema::create('community_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['general', 'course', 'instructor', 'feature_request', 'bug_report']);
            $table->string('title');
            $table->text('message');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_response')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('attachments')->nullable();
            $table->integer('rating')->nullable(); // 1-5 stars for course/instructor feedback
            $table->timestamps();
        });

        // Community Reports (for moderation)
        Schema::create('community_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->morphs('reportable'); // Can report threads, replies, activities, etc.
            $table->enum('reason', ['spam', 'inappropriate', 'harassment', 'plagiarism', 'other']);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->foreignId('moderator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('moderator_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_reports');
        Schema::dropIfExists('community_feedback');
    }
};