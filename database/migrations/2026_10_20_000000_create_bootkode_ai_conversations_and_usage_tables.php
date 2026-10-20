<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('title', 120);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'last_message_at']);
        });

        Schema::create('ai_conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant']);
            $table->longText('content');
            $table->json('context')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('ai_usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('feature', 60);
            $table->string('provider', 40);
            $table->string('status', 40);
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('error_reason', 60)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'feature', 'created_at']);
            $table->index(['provider', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_records');
        Schema::dropIfExists('ai_conversation_messages');
        Schema::dropIfExists('ai_conversations');
    }
};
