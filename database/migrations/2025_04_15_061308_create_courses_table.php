<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    public function up()
    {
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

            // Pricing and access (ONLY IN COURSES)
            $table->decimal('price', 8, 2)->default(0);
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

            // Media fields for course-level content
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
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
