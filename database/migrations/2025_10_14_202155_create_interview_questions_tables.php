<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Question Bank for storing interview questions
        Schema::create('interview_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // Question details
            $table->text('question');
            $table->enum('type', [
                'technical', 'behavioral', 'case_study', 'system_design', 
                'coding', 'hr', 'situational'
            ])->default('technical');
            $table->enum('difficulty_level', [
                'beginner', 'intermediate', 'advanced', 'expert'
            ])->default('intermediate');
            
            // Question format
            $table->enum('answer_type', [
                'text', 'multiple_choice', 'coding', 'file_upload'
            ])->default('text');
            
            // Multiple choice options (if applicable)
            $table->json('options')->nullable(); // For MCQ
            $table->text('correct_answer')->nullable(); // For MCQ or reference answer
            
            // Evaluation criteria
            $table->json('keywords')->nullable(); // Keywords to look for in answer
            $table->integer('max_points')->default(10);
            $table->integer('time_limit')->default(300); // seconds
            
            // Categorization
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->string('industry')->nullable();
            $table->string('job_role')->nullable();
            
            // Sample/ideal answer for evaluation
            $table->text('sample_answer')->nullable();
            $table->json('evaluation_rubric')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Usage statistics
            $table->unsignedInteger('times_used')->default(0);
            $table->decimal('avg_score', 5, 2)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['type', 'difficulty_level', 'is_active']);
            $table->index(['category', 'is_approved']);
            $table->index('created_by');
        });

        // Question Sets/Templates
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
            
            // Set configuration
            $table->integer('total_questions')->default(0);
            $table->integer('estimated_duration')->default(60); // minutes
            $table->json('question_distribution')->nullable(); // Distribution by type
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_template')->default(false);
            $table->boolean('is_public')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'is_active']);
            $table->index('created_by');
        });

        // Pivot table for question sets
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

        // Add question_set_id to mock_interviews table
        Schema::table('mock_interviews', function (Blueprint $table) {
            $table->foreignId('question_set_id')->nullable()->after('course_id')
                ->constrained('interview_question_sets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('mock_interviews', function (Blueprint $table) {
            $table->dropForeign(['question_set_id']);
            $table->dropColumn('question_set_id');
        });
        
        Schema::dropIfExists('interview_question_set_items');
        Schema::dropIfExists('interview_question_sets');
        Schema::dropIfExists('interview_questions');
    }
};