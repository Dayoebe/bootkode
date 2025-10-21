<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pages
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('excerpt', 500)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            
            $table->string('meta_title', 60)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('no_index')->default(false);
            
            $table->string('template', 50)->default('default');
            $table->json('custom_css')->nullable();
            $table->json('custom_js')->nullable();
            
            $table->json('page_blocks')->nullable();
            $table->json('shortcodes')->nullable();
            $table->json('settings')->nullable();
            
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->integer('view_count')->default(0);
            $table->decimal('avg_time_on_page', 8, 2)->nullable();
            $table->integer('bounce_rate')->nullable();
            
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamp('last_reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            $table->index(['status', 'published_at']);
            $table->index(['slug']);
            $table->index('created_by');
            $table->fullText(['title', 'content', 'meta_description']);
        });

        // Page Media
        Schema::create('page_media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('file_path');
            $table->integer('file_size');
            $table->enum('media_type', ['image', 'video', 'audio', 'document', 'other'])->default('image');
            
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            
            $table->boolean('is_optimized')->default(false);
            $table->json('thumbnails')->nullable();
            $table->string('cdn_url')->nullable();
            
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            $table->string('folder', 100)->default('uploads');
            $table->json('tags')->nullable();
            
            $table->foreignId('uploaded_by')->constrained('users');
            
            $table->timestamps();
            
            $table->index(['media_type', 'folder']);
            $table->index('uploaded_by');
            $table->index('mime_type');
        });

        // Page Media Attachments
        Schema::create('page_media_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->foreignId('media_id')->constrained('page_media')->onDelete('cascade');
            $table->string('context', 50)->default('content');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['page_id', 'media_id', 'context']);
            $table->index(['page_id', 'context']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_media_attachments');
        Schema::dropIfExists('page_media');
        Schema::dropIfExists('pages');
    }
};
