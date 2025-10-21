<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Interview Questions
        Schema::create('interview_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->text('question');
            $table->enum('type', [
                'technical', 'behavioral', 'case_study', 'system_design', 
                'coding', 'hr', 'situational'
            ])->default('technical');
            $table->enum('difficulty_level', [
                'beginner', 'intermediate', 'advanced', 'expert'
            ])->default('intermediate');
            
            $table->enum('answer_type', [
                'text', 'multiple_choice', 'coding', 'file_upload'
            ])->default('text');
            
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            
            $table->json('keywords')->nullable();
            $table->integer('max_points')->default(10);
            $table->integer('time_limit')->default(300);
            
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->string('industry')->nullable();
            $table->string('job_role')->nullable();
            
            $table->text('sample_answer')->nullable();
            $table->json('evaluation_rubric')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->unsignedInteger('times_used')->default(0);
            $table->decimal('avg_score', 5, 2)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'difficulty_level', 'is_active']);
            $table->index(['category', 'is_approved']);
            $table->index('created_by');
        });

        // Interview Question Sets
        Schema::create('interview_question_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', [
                'technical', 'behavioral', 'case_study', 'system_design', 
                'coding', 'hr', 'mixed'
            ])->default('technical');
            $table->enum('difficulty_level', [
                'beginner', 'intermediate', 'advanced', 'expert'
            ])->default('intermediate');
            
            $table->integer('total_questions')->default(0);
            $table->integer('estimated_duration')->default(60);
            $table->json('question_distribution')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_template')->default(false);
            $table->boolean('is_public')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'is_active']);
            $table->index('created_by');
        });

        // Interview Question Set Items
        Schema::create('interview_question_set_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_set_id')->constrained('interview_question_sets')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('interview_questions')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->integer('points')->default(10);
            $table->timestamps();
            
            $table->unique(['question_set_id', 'question_id']);
            $table->index('order');
        });

        // Mock Interviews
        Schema::create('mock_interviews', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('question_set_id')->nullable()->constrained('interview_question_sets')->onDelete('set null');
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('original_interview_id')->nullable()->constrained('mock_interviews')->onDelete('cascade');
            
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['technical', 'behavioral', 'case_study', 'system_design', 'coding', 'hr', 'custom'])->default('technical');
            $table->enum('format', ['text', 'voice', 'video', 'mixed'])->default('text');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'missed'])->default('scheduled');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            
            $table->string('industry')->nullable();
            $table->string('job_role')->nullable();
            $table->string('company_type')->nullable();
            $table->integer('estimated_duration_minutes')->default(60);
            
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->json('questions')->nullable();
            $table->json('custom_questions')->nullable();
            $table->json('question_order')->nullable();
            $table->integer('time_per_question')->nullable();
            $table->boolean('allow_retakes')->default(false);
            $table->integer('max_retakes')->default(0);
            $table->integer('retake_count')->default(0);
            $table->integer('auto_submit_timeout')->nullable();
            
            $table->boolean('is_premium')->default(false);
            $table->json('premium_features')->nullable();
            $table->boolean('ai_feedback_enabled')->default(false);
            $table->boolean('video_recording_enabled')->default(false);
            $table->boolean('detailed_analytics_enabled')->default(false);
            $table->json('custom_branding')->nullable();
            
            $table->json('user_responses')->nullable();
            $table->json('response_times')->nullable();
            $table->json('audio_recordings')->nullable();
            $table->json('video_recordings')->nullable();
            $table->json('screen_recordings')->nullable();
            
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->decimal('technical_score', 5, 2)->nullable();
            $table->decimal('communication_score', 5, 2)->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->decimal('problem_solving_score', 5, 2)->nullable();
            $table->decimal('cultural_fit_score', 5, 2)->nullable();
            
            $table->json('ai_feedback')->nullable();
            $table->json('interviewer_feedback')->nullable();
            $table->json('improvement_suggestions')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            
            $table->decimal('completion_rate', 5, 2)->nullable();
            $table->decimal('avg_response_time', 8, 2)->nullable();
            $table->integer('pause_count')->default(0);
            $table->integer('revision_count')->default(0);
            $table->json('confidence_metrics')->nullable();
            $table->json('speech_analysis')->nullable();
            $table->json('emotion_analysis')->nullable();
            $table->decimal('eye_contact_score', 5, 2)->nullable();
            $table->decimal('body_language_score', 5, 2)->nullable();
            
            $table->json('metadata')->nullable();
            $table->json('settings')->nullable();
            $table->json('tags')->nullable();
            
            $table->boolean('is_practice')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(false);
            
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('attempts_count')->default(0);
            $table->decimal('success_rate', 5, 2)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'type']);
            $table->index(['scheduled_at', 'status']);
            $table->index(['type', 'difficulty_level']);
            $table->index(['is_premium', 'is_featured']);
            $table->index(['industry', 'job_role']);
            $table->index('created_at');
            $table->index('overall_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_interviews');
        Schema::dropIfExists('interview_question_set_items');
        Schema::dropIfExists('interview_question_sets');
        Schema::dropIfExists('interview_questions');
    }
};
