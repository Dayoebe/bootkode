<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('file_path');
            $table->integer('file_size');
            $table->enum('media_type', ['image', 'video', 'audio', 'document', 'other'])->default('image');
            
            // Image-specific fields
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            
            // SEO & Optimization
            $table->boolean('is_optimized')->default(false);
            $table->json('thumbnails')->nullable(); // Different sizes for images
            $table->string('cdn_url')->nullable();
            
            // Usage tracking
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            // Organization
            $table->string('folder', 100)->default('uploads');
            $table->json('tags')->nullable();
            
            // User tracking
            $table->foreignId('uploaded_by')->constrained('users');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['media_type', 'folder']);
            $table->index('uploaded_by');
            $table->index('mime_type');
        });

        // Pivot table for page-media relationships
        Schema::create('page_media_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->foreignId('media_id')->constrained('page_media')->onDelete('cascade');
            $table->string('context', 50)->default('content'); // content, featured, gallery, etc.
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
    }
};