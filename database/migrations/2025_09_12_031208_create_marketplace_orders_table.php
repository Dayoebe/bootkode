<?php

// Migration 2: create_marketplace_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('marketplace_items')->onDelete('cascade');
            
            // Order details
            $table->enum('status', [
                'pending', 'confirmed', 'processing', 'completed', 
                'cancelled', 'refunded', 'failed'
            ])->default('pending');
            $table->enum('payment_status', [
                'unpaid', 'paid', 'partially_refunded', 'refunded', 'failed'
            ])->default('unpaid');
            
            // Pricing
            $table->decimal('item_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('NGN');
            
            // Commission & Revenue Split
            $table->decimal('platform_commission_rate', 5, 2)->default(20.00); // 20%
            $table->decimal('platform_commission', 10, 2)->default(0);
            $table->decimal('vendor_earning', 10, 2)->default(0);
            
            // Payment & Transaction details
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('payment_details')->nullable(); // Store payment gateway response
            
            // Flexible order details (booking time for services, download links, etc.)
            $table->json('details')->nullable();
            
            // Service booking specific
            $table->timestamp('scheduled_at')->nullable(); // For service bookings
            $table->timestamp('completed_at')->nullable();
            
            // Customer details snapshot (in case user data changes)
            $table->json('customer_details')->nullable();
            
            // Notes & Communication
            $table->text('customer_notes')->nullable();
            $table->text('vendor_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Fulfillment
            $table->boolean('is_delivered')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->json('delivery_details')->nullable();
            
            // Timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['customer_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('created_at');
            $table->index('order_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketplace_orders');
    }
};