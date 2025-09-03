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
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['document', 'presentation', 'worksheet', 'template', 'guide', 'other'])->default('document');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->string('original_filename')->nullable();
            $table->text('tags')->nullable();
            $table->boolean('is_public')->default(true);
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index(['course_id', 'type']);
            $table->index(['created_by', 'status']);
            $table->index(['is_public', 'status']);
            $table->fullText(['title', 'description', 'content', 'tags']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
    }
};