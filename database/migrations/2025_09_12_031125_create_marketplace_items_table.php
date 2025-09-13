<?php
// Migration 1: create_marketplace_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marketplace_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->enum('type', ['course', 'resource', 'service'])->default('course');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'suspended'])->default('draft');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('NGN');
            $table->boolean('is_digital')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable(); // Multiple images
            $table->json('files')->nullable(); // Downloadable files for resources
            $table->json('categories')->nullable(); // Multiple categories
            $table->json('tags')->nullable(); // SEO tags
            $table->json('metadata')->nullable(); // Flexible data storage
            
            // SEO Fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();
            
            // Stats
            $table->integer('views_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            
            // Service specific fields (duration, availability)
            $table->integer('duration_minutes')->nullable(); // For services
            $table->json('availability')->nullable(); // For services booking
            
            // Admin fields
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'type']);
            $table->index(['vendor_id', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index('created_at');
            $table->fullText(['title', 'description', 'keywords']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketplace_items');
    }
};
