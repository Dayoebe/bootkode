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
        Schema::create('video_libraries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url');
            $table->enum('video_type', ['upload', 'youtube', 'vimeo', 'external'])->default('upload');
            $table->string('thumbnail')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->enum('category', ['lecture', 'tutorial', 'demo', 'webinar', 'interview', 'presentation', 'other'])->default('tutorial');
            $table->text('tags')->nullable();
            $table->boolean('is_public')->default(true);
            $table->enum('quality', ['480p', '720p', '1080p', '1440p', '2160p'])->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->enum('status', ['processing', 'published', 'draft', 'archived', 'private'])->default('published');
            $table->timestamp('published_at')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('captions_file')->nullable();
            $table->longText('transcript')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'status']);
            $table->index(['video_type', 'status']);
            $table->index(['course_id', 'status']);
            $table->index(['uploaded_by', 'status']);
            $table->index(['is_public', 'status']);
            $table->index(['featured', 'status']);
            $table->fullText(['title', 'description', 'tags']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_libraries');
    }
};