<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Main mentorship relationships table
        Schema::create('mentorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentee_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, active, completed, cancelled, rejected
            $table->text('request_message')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('goals')->nullable();
            $table->json('expectations')->nullable();
            $table->integer('duration_weeks')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['mentor_id', 'status']);
            $table->index(['mentee_id', 'status']);
            $table->unique(['mentor_id', 'mentee_id', 'status'], 'unique_active_mentorship');
        });

        // Mentor profiles table
        Schema::create('mentor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_available')->default(true);
            $table->text('bio')->nullable();
            $table->json('specializations')->nullable(); // Array of expertise areas
            $table->json('skills')->nullable();
            $table->json('industries')->nullable();
            $table->json('languages')->nullable();
            $table->string('experience_level')->nullable(); // junior, mid, senior, expert
            $table->integer('years_experience')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->boolean('offers_free_sessions')->default(false);
            $table->integer('max_mentees')->default(5);
            $table->integer('current_mentees')->default(0);
            $table->json('availability_schedule')->nullable(); // Weekly schedule
            $table->string('timezone')->nullable();
            $table->json('communication_preferences')->nullable(); // video, audio, text, etc.
            $table->text('mentoring_approach')->nullable();
            $table->json('certifications')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('github_profile')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_mentees')->default(0);
            $table->integer('total_sessions')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->json('achievements')->nullable();
            $table->timestamps();

            $table->index(['is_available', 'is_verified']);
            $table->index('rating');
        });

        // Mentorship sessions table
        Schema::create('mentorship_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('general'); // general, code_review, project_guidance, career_advice
            $table->string('format')->default('video'); // video, audio, text, screen_share
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, cancelled, missed
            $table->timestamp('scheduled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('agenda')->nullable();
            $table->json('materials')->nullable(); // Links, documents, etc.
            $table->text('session_notes')->nullable();
            $table->json('action_items')->nullable();
            $table->text('mentor_feedback')->nullable();
            $table->text('mentee_feedback')->nullable();
            $table->decimal('mentor_rating', 3, 2)->nullable();
            $table->decimal('mentee_rating', 3, 2)->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('recording_url')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('is_billable')->default(false);
            $table->decimal('session_cost', 8, 2)->nullable();
            $table->string('payment_status')->nullable(); // pending, paid, failed
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['mentorship_id', 'status']);
            $table->index('scheduled_at');
        });

        // Code review requests table
        Schema::create('code_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('pending'); // pending, in_review, completed, declined
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->json('technologies')->nullable();
            $table->string('repository_url')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('pull_request_url')->nullable();
            $table->json('files_to_review')->nullable();
            $table->text('specific_questions')->nullable();
            $table->json('code_snippets')->nullable();
            $table->text('review_feedback')->nullable();
            $table->json('suggestions')->nullable();
            $table->decimal('code_quality_score', 3, 2)->nullable();
            $table->json('improvement_areas')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('started_review_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('estimated_review_time')->nullable(); // in minutes
            $table->integer('actual_review_time')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'priority']);
            $table->index('requested_at');
        });

        // Mentorship reviews and ratings
        Schema::create('mentorship_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade');
            $table->string('type')->default('mentorship'); // mentorship, session, code_review
            $table->foreignId('session_id')->nullable()->constrained('mentorship_sessions')->onDelete('cascade');
            $table->decimal('overall_rating', 3, 2);
            $table->decimal('communication_rating', 3, 2)->nullable();
            $table->decimal('expertise_rating', 3, 2)->nullable();
            $table->decimal('helpfulness_rating', 3, 2)->nullable();
            $table->decimal('professionalism_rating', 3, 2)->nullable();
            $table->text('review_text')->nullable();
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->boolean('would_recommend')->default(true);
            $table->boolean('is_public')->default(true);
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['mentorship_id', 'reviewer_id', 'type'], 'unique_review_per_relationship');
            $table->index(['reviewee_id', 'overall_rating']);
        });

        // Mentor applications table
        Schema::create('mentor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, under_review, approved, rejected
            $table->text('motivation')->nullable();
            $table->json('experience_details')->nullable();
            $table->json('specializations')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('github_profile')->nullable();
            $table->json('certifications')->nullable();
            $table->json('references')->nullable();
            $table->text('teaching_philosophy')->nullable();
            $table->integer('expected_mentees')->nullable();
            $table->decimal('proposed_hourly_rate', 8, 2)->nullable();
            $table->json('availability')->nullable();
            $table->text('additional_info')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->json('documents')->nullable(); // Resume, certificates, etc.
            $table->timestamps();

            $table->index(['status', 'submitted_at']);
        });

        // Mentorship resources table
        Schema::create('mentorship_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type'); // guide, template, checklist, video, article
            $table->string('category'); // onboarding, communication, goal_setting, feedback
            $table->json('tags')->nullable();
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->boolean('is_public')->default(true);
            $table->json('target_audience')->nullable(); // mentors, mentees, both
            $table->integer('views_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'category']);
            $table->index(['is_public', 'is_featured']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mentorship_resources');
        Schema::dropIfExists('mentor_applications');
        Schema::dropIfExists('mentorship_reviews');
        Schema::dropIfExists('code_reviews');
        Schema::dropIfExists('mentorship_sessions');
        Schema::dropIfExists('mentor_profiles');
        Schema::dropIfExists('mentorships');
    }
};