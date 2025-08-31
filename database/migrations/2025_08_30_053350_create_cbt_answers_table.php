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
         // CBT Answers Table (Individual question responses)
         Schema::create('cbt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->json('selected_answer')->nullable(); // Store selected option(s)
            $table->text('text_answer')->nullable(); // For essay/text questions
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_awarded', 8, 2)->default(0);
            $table->integer('time_spent_seconds')->default(0);
            $table->datetime('answered_at')->nullable();
            $table->boolean('flagged_for_review')->default(false);
            $table->integer('answer_sequence')->default(0);
            $table->timestamps();
            
            $table->unique(['cbt_result_id', 'question_id']);
            $table->index(['question_id', 'is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_answers');
    }
};
