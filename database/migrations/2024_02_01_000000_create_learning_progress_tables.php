<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Course-User Pivot (Enrollments)
        Schema::create('course_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->decimal('progress', 5, 2)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->integer('time_spent_minutes')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index(['user_id', 'progress']);
            $table->index(['user_id', 'last_accessed_at']);
        });

        // Lesson-User Pivot
        Schema::create('lesson_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent_minutes')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'lesson_id']);
            $table->index(['user_id', 'completed_at']);
        });

        // Lesson Progress (detailed tracking)
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });

        // User Progress (granular)
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('assessment_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Learning Sessions
        Schema::create('learning_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->string('activity_type')->default('lesson');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'started_at']);
            $table->index(['user_id', 'activity_type']);
            $table->index(['course_id', 'started_at']);
        });

        // Learning Goals
        Schema::create('learning_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('goal_type');
            $table->string('goal_name');
            $table->integer('target_value');
            $table->integer('current_value')->default(0);
            $table->string('time_period');
            $table->boolean('is_active')->default(true);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'goal_type']);
        });

        // User Achievements
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('achievement_type');
            $table->string('achievement_name');
            $table->string('achievement_description');
            $table->string('achievement_icon')->nullable();
            $table->integer('achievement_value')->default(0);
            $table->timestamp('earned_at');
            $table->timestamps();

            $table->index(['user_id', 'achievement_type']);
            $table->index(['user_id', 'earned_at']);
        });

        // Wishlists
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['user_id', 'course_id']);
            $table->unique(['user_id', 'course_id']);
        });

        // Saved Resources
        Schema::create('saved_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->morphs('resourceable');
            $table->string('type');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'resourceable_type', 'resourceable_id']);
        });

        // Downloadable Contents
        Schema::create('downloadable_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->json('content_types')->nullable();
            $table->decimal('size_mb', 8, 2);
            $table->timestamp('downloaded_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloadable_contents');
        Schema::dropIfExists('saved_resources');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('learning_goals');
        Schema::dropIfExists('learning_sessions');
        Schema::dropIfExists('user_progress');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('lesson_user');
        Schema::dropIfExists('course_user');
    }
};
