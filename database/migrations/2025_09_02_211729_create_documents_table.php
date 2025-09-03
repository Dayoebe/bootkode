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
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6B7280'); // Hex color
            $table->string('icon')->default('fas fa-folder');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['slug', 'is_active']);
            $table->index('sort_order');
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->enum('type', [
                'guide', 'manual', 'tutorial', 'reference', 'policy', 
                'procedure', 'faq', 'article', 'whitepaper', 'case_study', 'other'
            ])->default('article');
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', [
                'draft', 'pending_review', 'published', 'archived', 'deprecated'
            ])->default('draft');
            $table->enum('visibility', [
                'public', 'private', 'restricted', 'internal'
            ])->default('public');
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
            $table->enum('difficulty_level', [
                'beginner', 'intermediate', 'advanced', 'expert'
            ])->default('beginner');
            $table->integer('estimated_reading_time')->nullable(); // in minutes
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_categories');
    }
};