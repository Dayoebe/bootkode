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
    {        // CBT Results Table
        Schema::create('cbt_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_id', 50)->unique();
            $table->integer('attempt_number')->default(1);
            
            // Exam Session Data
            $table->datetime('started_at');
            $table->datetime('submitted_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->integer('time_remaining_seconds')->nullable();
            $table->boolean('auto_submitted')->default(false);
            
            // Scores and Results
            $table->integer('total_questions')->default(0);
            $table->integer('answered_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('unanswered_questions')->default(0);
            $table->decimal('total_points', 8, 2)->default(0);
            $table->decimal('points_earned', 8, 2)->default(0);
            $table->decimal('percentage_score', 5, 2)->default(0);
            $table->boolean('passed')->default(false);
            $table->string('grade', 5)->nullable(); // A, B, C, D, F
            $table->integer('rank')->nullable();
            
            // Status and Flags
            $table->enum('status', ['in_progress', 'completed', 'abandoned', 'expired', 'disqualified'])->default('in_progress');
            $table->boolean('result_viewed')->default(false);
            $table->datetime('result_viewed_at')->nullable();
            $table->boolean('result_emailed')->default(false);
            $table->datetime('result_emailed_at')->nullable();
            $table->boolean('certificate_eligible')->default(false);
            
            // Analytics and Metadata
            $table->json('answer_sequence')->nullable(); // Track question answering pattern
            $table->json('time_analytics')->nullable(); // Time spent per question
            $table->json('browser_info')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['cbt_exam_id', 'user_id', 'attempt_number']);
            $table->index(['user_id', 'status']);
            $table->index(['cbt_exam_id', 'percentage_score']);
            $table->index(['passed', 'completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_results');
    }
};
