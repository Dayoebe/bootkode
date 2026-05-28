<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_learning_profiles')) {
            Schema::create('ai_learning_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('goal')->nullable();
                $table->json('skill_diagnosis')->nullable();
                $table->json('adaptive_path')->nullable();
                $table->json('course_recommendations')->nullable();
                $table->json('assessment_feedback')->nullable();
                $table->json('signals')->nullable();
                $table->timestamp('diagnosed_at')->nullable();
                $table->timestamps();

                $table->unique('user_id');
                $table->index('diagnosed_at');
            });
        }

        if (! Schema::hasTable('ai_tutor_messages')) {
            Schema::create('ai_tutor_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
                $table->text('question');
                $table->longText('answer');
                $table->string('source')->default('local_context');
                $table->json('context')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['course_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_tutor_messages');
        Schema::dropIfExists('ai_learning_profiles');
    }
};
