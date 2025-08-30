<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('job_portal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade');

            // Basic Job Information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            $table->text('company_description')->nullable();

            // Company Information
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_size')->nullable();
            $table->string('company_industry')->nullable();
            $table->json('company_social_links')->nullable();

            // Job Details
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'temporary', 'internship', 'freelance'])->default('full-time');
            $table->enum('work_type', ['on-site', 'remote', 'hybrid'])->default('on-site');
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'executive', 'director'])->default('mid');
            $table->string('category'); // IT, Marketing, Sales, etc.
            $table->json('skills_required')->nullable();
            $table->json('tags')->nullable();

            // Location & Salary
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

            // Application Information
            $table->enum('application_method', ['internal', 'email', 'external_link', 'phone'])->default('internal');
            $table->string('application_email')->nullable();
            $table->string('application_url')->nullable();
            $table->string('application_phone')->nullable();
            $table->text('application_instructions')->nullable();
            $table->json('required_documents')->nullable(); // CV, Cover Letter, Portfolio, etc.

            // Dates & Deadlines
            $table->timestamp('application_deadline')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->integer('positions_available')->default(1);

            // Premium Features
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->boolean('highlight_job')->default(false);
            $table->json('premium_features')->nullable();
            $table->timestamp('featured_until')->nullable();
            $table->timestamp('premium_until')->nullable();

            // Status & Visibility
            $table->enum('status', ['draft', 'active', 'paused', 'expired', 'filled', 'cancelled'])->default('draft');
            $table->boolean('is_public')->default(true);
            $table->boolean('auto_expire')->default(true);
            $table->timestamp('expires_at')->nullable();

            // Analytics & Engagement
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->integer('shortlisted_count')->default(0);
            $table->integer('interview_count')->default(0);
            $table->integer('hired_count')->default(0);
            $table->decimal('application_conversion_rate', 5, 2)->default(0);

            // AI & Matching Features
            $table->json('ai_keywords')->nullable();
            $table->decimal('ai_match_score', 5, 2)->nullable();
            $table->json('screening_questions')->nullable();
            $table->boolean('enable_ai_screening')->default(false);
            $table->json('auto_rejection_criteria')->nullable();

            // SEO & Marketing
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('structured_data')->nullable();

            // Advanced Features
            $table->json('custom_fields')->nullable();
            $table->json('interview_process')->nullable();
            $table->boolean('allow_remote_interview')->default(false);
            $table->json('video_interview_settings')->nullable();
            $table->string('referral_bonus')->nullable();
            $table->boolean('diversity_hiring')->default(false);
            $table->json('diversity_preferences')->nullable();

            // Internal Management
            $table->json('internal_notes')->nullable();
            $table->string('hiring_manager')->nullable();
            $table->json('team_members')->nullable(); // Users who can manage this job
            $table->string('department')->nullable();
            $table->string('job_code')->nullable();
            $table->integer('priority_score')->default(50);

            // Integration & Automation
            $table->json('integration_settings')->nullable();
            $table->boolean('sync_with_external')->default(false);
            $table->string('external_job_id')->nullable();
            $table->json('webhook_urls')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['status', 'is_public', 'expires_at']);
            $table->index(['category', 'location']);
            $table->index(['employment_type', 'work_type']);
            $table->index(['is_featured', 'is_premium']);
            $table->index('application_deadline');
            $table->fullText(['title', 'description', 'company_name']);
        });

        // Job Applications Table
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Application Data
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable();
            $table->json('additional_documents')->nullable();
            $table->json('custom_responses')->nullable(); // Responses to screening questions
            $table->decimal('match_score', 5, 2)->nullable();

            // Application Status
            $table->enum('status', ['pending', 'reviewing', 'shortlisted', 'interviewed', 'offered', 'hired', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->json('interview_schedule')->nullable();
            $table->json('feedback')->nullable();
            $table->decimal('rating', 3, 1)->nullable();

            // Tracking
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->json('activity_log')->nullable();

            $table->timestamps();
            $table->unique(['job_id', 'user_id']); // Prevent duplicate applications
        });

        // Job Saves/Bookmarks Table
        Schema::create('job_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('notes')->nullable();
            $table->timestamps();

            $table->unique(['job_id', 'user_id']);
        });

        // Job Categories Table
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
    }

    public function down()
    {
        Schema::dropIfExists('job_saves');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_categories');
        Schema::dropIfExists('job_portal');
    }
};