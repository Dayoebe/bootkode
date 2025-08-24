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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            
            // Question content
            $table->text('question_text');
            $table->enum('question_type', [
                'multiple_choice', 
                'true_false', 
                'short_answer', 
                'essay', 
                'fill_blank',
                'matching',
                'ordering',
                'drag_drop'
            ]);
            
            // Question options and answers (JSON)
            $table->json('options')->nullable(); // For multiple choice options
            $table->json('correct_answers')->nullable(); // Correct answer indices/values
            
            // Scoring and settings
            $table->decimal('points', 5, 2)->default(1.00);
            $table->text('explanation')->nullable(); // Feedback/explanation
            $table->boolean('is_required')->default(true);
            $table->integer('time_limit')->nullable(); // Time limit in seconds
            $table->integer('order')->default(0);
            
            // Additional metadata
            $table->enum('difficulty_level', ['easy', 'medium', 'hard', 'expert'])->default('medium');
            $table->json('tags')->nullable(); // Topic tags for organization
            $table->json('metadata')->nullable(); // Additional question-specific data
            
            // Performance tracking
            $table->integer('times_used')->default(0);
            $table->decimal('average_score', 5, 2)->nullable();
            $table->decimal('difficulty_index', 3, 2)->nullable(); // Calculated difficulty (0-1)
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['assessment_id', 'order']);
            $table->index(['question_type', 'difficulty_level']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};