<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Course Categories
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->onDelete('set null');

            // Basic information
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->integer('estimated_duration_minutes')->nullable();

            // Pricing and access
            $table->decimal('price', 8, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->string('currency', 3)->default('NGN');
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_free')->default(false);

            // Publishing and approval
            $table->boolean('has_offline_content')->default(false);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamp('scheduled_publish_at')->nullable();
            $table->timestamp('published_at')->nullable();

            // Course-specific content
            $table->text('target_audience')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->json('prerequisites')->nullable();
            $table->text('syllabus_overview')->nullable();
            $table->json('faqs')->nullable();
            $table->string('certificate_template')->nullable();
            $table->integer('completion_rate_threshold')->default(80);

            // Computed counters
            $table->integer('total_modules')->default(0);
            $table->integer('total_lessons')->default(0);
            $table->integer('total_projects')->default(0);
            $table->integer('total_assessments')->default(0);
            $table->boolean('has_projects')->default(false);
            $table->boolean('has_assessments')->default(false);

            // Media fields
            $table->json('images')->nullable();
            $table->json('documents')->nullable();
            $table->json('videos')->nullable();
            $table->json('external_links')->nullable();

            // Engagement metrics
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['is_published', 'is_approved']);
            $table->index(['instructor_id', 'is_published']);
            $table->index(['category_id', 'is_published']);
            $table->index('difficulty_level');
            $table->index('is_premium');
            $table->index('is_free');
            $table->fullText(['title', 'description'], 'courses_search_index');
        });

        // Sections
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->enum('type', ['section', 'module'])->default('section');
            $table->boolean('is_locked')->default(true);
            $table->timestamps();
        });

        // Lessons
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('content_type')->default('text');
            $table->text('text_content')->nullable();

            $table->string('video_url')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('order')->default(0);
            $table->decimal('size_mb', 10, 2)->nullable();

            $table->string('image_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('file_path')->nullable();

            $table->json('images')->nullable();
            $table->json('documents')->nullable();
            $table->json('audios')->nullable();
            $table->json('videos')->nullable();
            $table->json('external_links')->nullable();

            $table->timestamp('scheduled_publish_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->enum('completion_time_type', ['reading', 'watching', 'practice', 'total'])->default('reading');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');

            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);

            $table->timestamps();

            $table->index(['section_id', 'order']);
            $table->index('published_at');
            $table->fullText(['title', 'description'], 'lessons_search_index');
        });

        // Assessments
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->onDelete('set null');

            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedInteger('order')->default(0);
            $table->text('description')->nullable();
            $table->enum('type', ['quiz', 'project', 'assignment', 'qna'])->default('quiz');
            $table->integer('pass_percentage')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->datetime('deadline')->nullable();

            $table->string('project_type')->nullable();
            $table->json('required_skills')->nullable();
            $table->json('deliverables')->nullable();
            $table->json('resources')->nullable();

            $table->boolean('is_mandatory')->default(false);
            $table->integer('weight')->nullable();
            $table->boolean('allows_collaboration')->default(false);
            $table->text('evaluation_criteria')->nullable();
            $table->datetime('due_date')->nullable();
            $table->integer('max_score')->default(100);
            $table->unsignedInteger('max_attempts')->nullable();
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->text('instructions')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Questions
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');

            $table->text('question_text');
            $table->enum('question_type', [
                'multiple_choice', 
                'true_false', 
                'short_answer', 
                'essay', 
                'fill_blank',
                'matching',
                'ordering',
                'drag_drop',
                'qna_topic',
                'project_criteria',
                'assignment_question'
            ]);

            $table->json('options')->nullable();
            $table->json('correct_answers')->nullable();

            $table->decimal('points', 5, 2)->default(1.00);
            $table->text('explanation')->nullable();
            $table->boolean('is_required')->default(true);
            $table->integer('time_limit')->nullable();
            $table->integer('order')->default(0);

            $table->enum('difficulty_level', ['easy', 'medium', 'hard', 'expert'])->default('medium');
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();

            $table->integer('times_used')->default(0);
            $table->decimal('average_score', 5, 2)->nullable();
            $table->decimal('difficulty_index', 3, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['assessment_id', 'order']);
            $table->index(['question_type', 'difficulty_level']);
        });

        // Student Answers
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            
            $table->integer('attempt_number')->default(1);
            $table->json('question_order')->nullable();
            $table->json('exam_data')->nullable();
            $table->text('answer')->nullable();
            $table->decimal('points_earned', 8, 2)->default(0);
            $table->boolean('is_correct')->default(false);
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'assessment_id', 'attempt_number']);
            $table->index(['assessment_id', 'question_id']);
            $table->index(['user_id', 'question_id']);
        });

        // Course Reviews
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->text('instructor_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        // Review Replies
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('course_reviews')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('reply_text');
            $table->timestamps();
        });

        // Course Rejections
        Schema::create('course_rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('reason');
            $table->timestamps();
        });

        // Review Analytics
        Schema::create('review_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('response_count')->default(0);
            $table->decimal('response_rate', 5, 2)->default(0);
            $table->decimal('sentiment_score', 3, 2)->nullable();
            $table->json('keyword_frequencies')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'date']);
            $table->index('date');
        });

        // Review Reminders
        Schema::create('review_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('reminder_count')->default(0);
            $table->timestamp('last_reminded_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('unsubscribed')->default(false);
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index('last_reminded_at');
        });

        // Discount Codes
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2);
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('uses_per_user')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['code', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
        Schema::dropIfExists('review_reminders');
        Schema::dropIfExists('review_analytics');
        Schema::dropIfExists('course_rejections');
        Schema::dropIfExists('review_replies');
        Schema::dropIfExists('course_reviews');
        Schema::dropIfExists('student_answers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_categories');
    }
};
