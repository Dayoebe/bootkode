<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');

            // Basic lesson information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('content')->nullable();
            $table->string('content_type')->default('text');

            // Media and content
            $table->string('video_url')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('order')->default(0);
            $table->text('text_content')->nullable();
            $table->decimal('size_mb', 10, 2)->nullable();

            // Legacy single file paths (keeping for backward compatibility)
            $table->string('image_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('file_path')->nullable();

            // New multi-file JSON columns
            $table->json('images')->nullable();
            $table->json('documents')->nullable();
            $table->json('audios')->nullable();
            $table->json('videos')->nullable(); // Missing column
            $table->json('external_links')->nullable();

            // Pricing and access
            $table->boolean('is_free')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->decimal('price', 10, 2)->default(0);

            // Publishing and scheduling
            $table->timestamp('scheduled_publish_at')->nullable();
            $table->timestamp('published_at')->nullable();

            // Learning attributes
            $table->enum('completion_time_type', ['reading', 'watching', 'practice', 'total'])->default('reading');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');

            // Engagement metrics
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['section_id', 'order']);
            $table->index('published_at');
            $table->index('is_free');
            $table->index('is_premium');
        });

        // Add FULLTEXT index for search
        Schema::table('lessons', function (Blueprint $table) {
            $table->fullText(['title', 'description'], 'lessons_search_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};