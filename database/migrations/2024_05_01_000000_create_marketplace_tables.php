<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Marketplace Categories
        Schema::create('marketplace_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('fas fa-folder');
            $table->string('color', 7)->default('#6366f1');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
            $table->index('is_featured');
        });

        // Marketplace Items
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
            $table->json('images')->nullable();
            $table->json('files')->nullable();
            $table->json('categories')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();
            
            $table->integer('views_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            
            $table->integer('duration_minutes')->nullable();
            $table->json('availability')->nullable();
            
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'type']);
            $table->index(['vendor_id', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index('created_at');
            $table->fullText(['title', 'description', 'keywords']);
        });

        // Marketplace Item Categories (Pivot)
        Schema::create('marketplace_item_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('marketplace_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['marketplace_item_id', 'marketplace_category_id'], 'item_category_unique');
            $table->index('marketplace_item_id');
            $table->index('marketplace_category_id');
        });

        // Marketplace Orders
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('marketplace_items')->onDelete('cascade');
            
            $table->enum('status', [
                'pending', 'confirmed', 'processing', 'completed', 
                'cancelled', 'refunded', 'failed'
            ])->default('pending');
            $table->enum('payment_status', [
                'unpaid', 'paid', 'partially_refunded', 'refunded', 'failed'
            ])->default('unpaid');
            
            $table->decimal('item_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('NGN');
            
            $table->decimal('platform_commission_rate', 5, 2)->default(20.00);
            $table->decimal('platform_commission', 10, 2)->default(0);
            $table->decimal('vendor_earning', 10, 2)->default(0);
            
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('payment_details')->nullable();
            
            $table->json('details')->nullable();
            
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->json('customer_details')->nullable();
            
            $table->text('customer_notes')->nullable();
            $table->text('vendor_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->boolean('is_delivered')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->json('delivery_details')->nullable();
            
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['customer_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('created_at');
            $table->index('order_number');
        });

        // Product Reviews
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('reviewable');
            $table->integer('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_featured')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_approved');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('marketplace_orders');
        Schema::dropIfExists('marketplace_item_categories');
        Schema::dropIfExists('marketplace_items');
        Schema::dropIfExists('marketplace_categories');
    }
};
