<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Document Categories
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6B7280');
            $table->string('icon')->default('fas fa-folder');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['slug', 'is_active']);
            $table->index('sort_order');
        });

        // Documents
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->enum('type', ['guide', 'manual', 'tutorial', 'reference', 'policy', 'procedure', 'faq', 'article', 'whitepaper', 'case_study', 'other'])->default('article');
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'pending_review', 'published', 'archived', 'deprecated'])->default('draft');
            $table->enum('visibility', ['public', 'private', 'restricted', 'internal'])->default('public');
            $table->boolean('featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('tags')->nullable();
            $table->decimal('version', 4, 2)->default(1.0);
            $table->foreignId('parent_id')->nullable()->constrained('documents')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('attachments')->nullable();
            $table->json('related_documents')->nullable();
            $table->string('language', 5)->default('en');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->integer('estimated_reading_time')->nullable();
            $table->json('table_of_contents')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['visibility', 'status']);
            $table->index(['featured', 'status']);
            $table->index(['parent_id', 'order']);
            $table->index(['language', 'status']);
            $table->fullText(['title', 'content', 'excerpt', 'tags']);
        });

        // Video Libraries
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

        // Learning Materials
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

    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
        Schema::dropIfExists('video_libraries');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_categories');
    }
};
