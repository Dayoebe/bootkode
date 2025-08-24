<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->json('answer')->nullable(); // Store the student's answer
            $table->decimal('points_earned', 8, 2)->default(0);
            $table->boolean('is_correct')->default(false);
            $table->integer('time_spent')->nullable(); // in seconds
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->text('feedback')->nullable(); // Manual grading feedback
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'assessment_id', 'attempt_number']);
            $table->index(['assessment_id', 'question_id']);
            $table->index(['user_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};