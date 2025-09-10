<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('excerpt', 500)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            
            // SEO Fields
            $table->string('meta_title', 60)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('no_index')->default(false);
            
            // Template & Styling
            $table->string('template', 50)->default('default');
            $table->json('custom_css')->nullable();
            $table->json('custom_js')->nullable();
            
            // Advanced Features
            $table->json('page_blocks')->nullable(); // For block-based content
            $table->json('shortcodes')->nullable(); // For shortcode processing
            $table->json('settings')->nullable(); // Custom page settings
            
            // Publishing & Scheduling
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Analytics & Performance
            $table->integer('view_count')->default(0);
            $table->decimal('avg_time_on_page', 8, 2)->nullable();
            $table->integer('bounce_rate')->nullable();
            
            // User Management
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamp('last_reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'published_at']);
            $table->index(['slug']);
            $table->index('created_by');
            $table->fullText(['title', 'content', 'meta_description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};