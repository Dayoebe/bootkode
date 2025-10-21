<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Job Categories
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('#3b82f6');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('jobs_count')->default(0);
            $table->timestamps();
        });

        // Job Portal
        Schema::create('job_portal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            $table->text('company_description')->nullable();
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_size')->nullable();
            $table->string('company_industry')->nullable();
            $table->json('company_social_links')->nullable();
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'temporary', 'internship', 'freelance'])->default('full-time');
            $table->enum('work_type', ['on-site', 'remote', 'hybrid'])->default('on-site');
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'executive', 'director'])->default('mid');
            $table->string('category');
            $table->json('skills_required')->nullable();
            $table->json('tags')->nullable();
            $table->string('location');
            $table->string('country')->default('Nigeria');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->enum('salary_currency', ['NGN', 'USD', 'GBP', 'EUR'])->default('NGN');
            $table->enum('salary_period', ['hourly', 'daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->boolean('salary_negotiable')->default(false);
            $table->boolean('hide_salary')->default(false);
            $table->enum('application_method', ['internal', 'email', 'external_link', 'phone'])->default('internal');
            $table->string('application_email')->nullable();
            $table->string('application_url')->nullable();
            $table->string('application_phone')->nullable();
            $table->text('application_instructions')->nullable();
            $table->json('required_documents')->nullable();
            $table->timestamp('application_deadline')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->integer('positions_available')->default(1);
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->boolean('highlight_job')->default(false);
            $table->json('premium_features')->nullable();
            $table->timestamp('featured_until')->nullable();
            $table->timestamp('premium_until')->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'expired', 'filled', 'cancelled'])->default('draft');
            $table->boolean('is_public')->default(true);
            $table->boolean('auto_expire')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->integer('shortlisted_count')->default(0);
            $table->integer('interview_count')->default(0);
            $table->integer('hired_count')->default(0);
            $table->decimal('application_conversion_rate', 5, 2)->default(0);
            $table->json('ai_keywords')->nullable();
            $table->decimal('ai_match_score', 5, 2)->nullable();
            $table->json('screening_questions')->nullable();
            $table->boolean('enable_ai_screening')->default(false);
            $table->json('auto_rejection_criteria')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('structured_data')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('interview_process')->nullable();
            $table->boolean('allow_remote_interview')->default(false);
            $table->json('video_interview_settings')->nullable();
            $table->string('referral_bonus')->nullable();
            $table->boolean('diversity_hiring')->default(false);
            $table->json('diversity_preferences')->nullable();
            $table->json('internal_notes')->nullable();
            $table->string('hiring_manager')->nullable();
            $table->json('team_members')->nullable();
            $table->string('department')->nullable();
            $table->string('job_code')->nullable();
            $table->integer('priority_score')->default(50);
            $table->json('integration_settings')->nullable();
            $table->boolean('sync_with_external')->default(false);
            $table->string('external_job_id')->nullable();
            $table->json('webhook_urls')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_public', 'expires_at']);
            $table->index(['category', 'location']);
            $table->index(['employment_type', 'work_type']);
            $table->index(['is_featured', 'is_premium']);
            $table->index('application_deadline');
            $table->fullText(['title', 'description', 'company_name']);
        });

        // Job Applications
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable();
            $table->json('additional_documents')->nullable();
            $table->json('custom_responses')->nullable();
            $table->decimal('match_score', 5, 2)->nullable();
            $table->enum('status', ['pending', 'reviewing', 'shortlisted', 'interviewed', 'offered', 'hired', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->json('interview_schedule')->nullable();
            $table->json('feedback')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->json('activity_log')->nullable();
            $table->timestamps();
            $table->unique(['job_id', 'user_id']);
        });

        // Job Saves
        Schema::create('job_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('notes')->nullable();
            $table->timestamps();

            $table->unique(['job_id', 'user_id']);
        });

        // Portfolios
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('category');
            $table->enum('status', ['completed', 'in-progress', 'planning', 'on-hold'])->default('completed');
            $table->string('project_url')->nullable();
            $table->string('client_name')->nullable();
            $table->text('technologies');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('image_path')->nullable();
            $table->json('additional_images')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'is_featured']);
            $table->index('slug');
        });

        // Resume Profiles
        Schema::create('resume_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name')->nullable();
            $table->string('professional_title')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->text('professional_summary')->nullable();
            $table->string('profile_image')->nullable();
            $table->json('work_experience')->nullable();
            $table->json('education')->nullable();
            $table->json('skills')->nullable();
            $table->json('projects')->nullable();
            $table->json('certifications')->nullable();
            $table->json('languages')->nullable();
            $table->json('references')->nullable();
            $table->json('custom_sections')->nullable();
            $table->string('selected_template')->default('modern');
            $table->string('color_scheme')->default('professional');
            $table->string('font_family')->default('inter');
            $table->json('section_order')->nullable();
            $table->json('section_visibility')->nullable();
            $table->boolean('show_profile_image')->default(true);
            $table->boolean('is_public')->default(false);
            $table->string('public_slug')->nullable()->unique();
            $table->boolean('is_premium')->default(false);
            $table->timestamp('last_edited_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_public']);
            $table->index('public_slug');
            $table->index('selected_template');
        });

        // Mentorships
        Schema::create('mentorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentee_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending');
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
        });

        // Mentor Profiles
        Schema::create('mentor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_available')->default(true);
            $table->text('bio')->nullable();
            $table->json('specializations')->nullable();
            $table->json('skills')->nullable();
            $table->json('industries')->nullable();
            $table->json('languages')->nullable();
            $table->string('experience_level')->nullable();
            $table->integer('years_experience')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->boolean('offers_free_sessions')->default(false);
            $table->integer('max_mentees')->default(5);
            $table->integer('current_mentees')->default(0);
            $table->json('availability_schedule')->nullable();
            $table->string('timezone')->nullable();
            $table->json('communication_preferences')->nullable();
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

        // Mentorship Sessions
        Schema::create('mentorship_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('general');
            $table->string('format')->default('video');
            $table->string('status')->default('scheduled');
            $table->timestamp('scheduled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('agenda')->nullable();
            $table->json('materials')->nullable();
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
            $table->string('payment_status')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['mentorship_id', 'status']);
            $table->index('scheduled_at');
        });

        // Code Reviews
        Schema::create('code_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('pending');
            $table->string('priority')->default('medium');
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
            $table->integer('estimated_review_time')->nullable();
            $table->integer('actual_review_time')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'priority']);
            $table->index('requested_at');
        });

        // Mentorship Reviews
        Schema::create('mentorship_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade');
            $table->string('type')->default('mentorship');
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

            $table->index(['reviewee_id', 'overall_rating']);
        });

        // Mentor Applications
        Schema::create('mentor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
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
            $table->json('documents')->nullable();
            $table->timestamps();

            $table->index(['status', 'submitted_at']);
        });

        // Mentorship Resources
        Schema::create('mentorship_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type');
            $table->string('category');
            $table->json('tags')->nullable();
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->boolean('is_public')->default(true);
            $table->json('target_audience')->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('mentorship_resources');
        Schema::dropIfExists('mentor_applications');
        Schema::dropIfExists('mentorship_reviews');
        Schema::dropIfExists('code_reviews');
        Schema::dropIfExists('mentorship_sessions');
        Schema::dropIfExists('mentor_profiles');
        Schema::dropIfExists('mentorships');
        Schema::dropIfExists('resume_profiles');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('job_saves');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_portal');
        Schema::dropIfExists('job_categories');
    }
};
