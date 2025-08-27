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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic project information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('category');
            $table->enum('status', ['completed', 'in-progress', 'planning', 'on-hold'])->default('completed');
            
            // Project details
            $table->string('project_url')->nullable();
            $table->string('client_name')->nullable();
            $table->text('technologies'); // JSON or comma-separated
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Media
            $table->string('image_path')->nullable();
            $table->json('additional_images')->nullable(); // For multiple images
            
            // Analytics
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            
            // SEO and sharing
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();
            
            // Display options
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'is_featured']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};