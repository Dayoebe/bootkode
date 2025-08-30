<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mock_interviews', function (Blueprint $table) {
            $table->id();
            
            // Core relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('original_interview_id')->nullable()->constrained('mock_interviews')->onDelete('cascade');
            
            // Basic information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', [
                'technical', 'behavioral', 'case_study', 'system_design', 
                'coding', 'hr', 'custom'
            ])->default('technical');
            $table->enum('format', ['text', 'voice', 'video', 'mixed'])->default('text');
            $table->enum('status', [
                'scheduled', 'in_progress', 'completed', 'cancelled', 'missed'
            ])->default('scheduled');
            $table->enum('difficulty_level', [
                'beginner', 'intermediate', 'advanced', 'expert'
            ])->default('intermediate');
            
            // Interview context
            $table->string('industry')->nullable();
            $table->string('job_role')->nullable();
            $table->string('company_type')->nullable();
            $table->integer('estimated_duration_minutes')->default(60);
            
            // Scheduling & timing
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Interview configuration
            $table->json('questions')->nullable(); // Standard questions
            $table->json('custom_questions')->nullable(); // Custom questions
            $table->json('question_order')->nullable(); // Order of questions
            $table->integer('time_per_question')->nullable(); // In seconds
            $table->boolean('allow_retakes')->default(false);
            $table->integer('max_retakes')->default(0);
            $table->integer('retake_count')->default(0);
            $table->integer('auto_submit_timeout')->nullable(); // In minutes
            
            // Premium features
            $table->boolean('is_premium')->default(false);
            $table->json('premium_features')->nullable();
            $table->boolean('ai_feedback_enabled')->default(false);
            $table->boolean('video_recording_enabled')->default(false);
            $table->boolean('detailed_analytics_enabled')->default(false);
            $table->json('custom_branding')->nullable();
            
            // User responses & recordings
            $table->json('user_responses')->nullable();
            $table->json('response_times')->nullable(); // Time taken per question
            $table->json('audio_recordings')->nullable(); // Paths to audio files
            $table->json('video_recordings')->nullable(); // Paths to video files
            $table->json('screen_recordings')->nullable(); // For coding interviews
            
            // Scoring & evaluation
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->decimal('technical_score', 5, 2)->nullable();
            $table->decimal('communication_score', 5, 2)->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->decimal('problem_solving_score', 5, 2)->nullable();
            $table->decimal('cultural_fit_score', 5, 2)->nullable();
            
            // Feedback & suggestions
            $table->json('ai_feedback')->nullable();
            $table->json('interviewer_feedback')->nullable();
            $table->json('improvement_suggestions')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            
            // Advanced analytics (Premium features)
            $table->decimal('completion_rate', 5, 2)->nullable();
            $table->decimal('avg_response_time', 8, 2)->nullable(); // In seconds
            $table->integer('pause_count')->default(0);
            $table->integer('revision_count')->default(0);
            $table->json('confidence_metrics')->nullable();
            $table->json('speech_analysis')->nullable(); // Speech patterns, pace, etc.
            $table->json('emotion_analysis')->nullable(); // Emotional state analysis
            $table->decimal('eye_contact_score', 5, 2)->nullable();
            $table->decimal('body_language_score', 5, 2)->nullable();
            
            // Additional metadata
            $table->json('metadata')->nullable(); // Additional custom data
            $table->json('settings')->nullable(); // Interview-specific settings
            $table->json('tags')->nullable(); // For categorization
            
            // Status flags
            $table->boolean('is_practice')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(false);
            
            // Statistics
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('attempts_count')->default(0);
            $table->decimal('success_rate', 5, 2)->nullable();
            
            // Timestamps & soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'type']);
            $table->index(['scheduled_at', 'status']);
            $table->index(['type', 'difficulty_level']);
            $table->index(['is_premium', 'is_featured']);
            $table->index(['industry', 'job_role']);
            $table->index('created_at');
            $table->index('overall_score');
            
            // Composite indexes for common queries
            $table->index(['user_id', 'status', 'scheduled_at']);
            $table->index(['type', 'difficulty_level', 'is_public']);
            $table->index(['is_premium', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_interviews');
    }
};