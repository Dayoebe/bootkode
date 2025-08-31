<?php

// Migration 1: Create CBT exams and results tables
// File: 2025_08_30_120000_create_cbt_exams_and_results_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // CBT Exams Table
        Schema::create('cbt_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('exam_code', 20)->unique();
            $table->enum('exam_type', ['practice', 'mock', 'final', 'certification'])->default('practice');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            
            // Exam Configuration
            $table->integer('duration_minutes')->default(60);
            $table->integer('total_questions')->default(0);
            $table->decimal('pass_percentage', 5, 2)->default(70.00);
            $table->integer('max_attempts')->default(3);
            $table->integer('questions_per_page')->default(1);
            $table->boolean('randomize_questions')->default(true);
            $table->boolean('randomize_options')->default(true);
            $table->boolean('show_results_immediately')->default(false);
            $table->boolean('allow_review')->default(true);
            $table->boolean('allow_navigation')->default(true);
            
            // Scheduling
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->json('available_days')->nullable(); // Array of days when exam is available
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            // Result Configuration
            $table->enum('result_delivery', ['instant', 'scheduled', 'manual'])->default('instant');
            $table->datetime('result_release_date')->nullable();
            $table->boolean('email_results')->default(false);
            $table->boolean('show_correct_answers')->default(false);
            $table->boolean('show_explanations')->default(false);
            
            // Status and Publishing
            $table->boolean('is_published')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('max_participants')->nullable();
            $table->text('instructions')->nullable();
            $table->json('exam_settings')->nullable();
            
            // Metadata
            $table->json('tags')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('attempts_count')->default(0);
            $table->decimal('average_score', 5, 2)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['course_id', 'is_published', 'is_active']);
            $table->index(['exam_type', 'difficulty_level']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_answers');
        Schema::dropIfExists('cbt_results');
        Schema::dropIfExists('cbt_exam_questions');
        Schema::dropIfExists('cbt_exams');
    }
};