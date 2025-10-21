<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Affiliates
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('referral_code', 20)->unique();
            $table->decimal('commission_rate', 5, 2)->default(30.00);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->decimal('total_earned', 10, 2)->default(0);
            $table->integer('total_referrals')->default(0);
            $table->integer('active_referrals')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'commission_rate']);
            $table->index('referral_code');
        });

        // Referrals
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->decimal('total_commission_earned', 10, 2)->default(0);
            $table->integer('courses_purchased')->default(0);
            $table->timestamp('first_purchase_at')->nullable();
            $table->timestamp('last_purchase_at')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['affiliate_id', 'referred_user_id']);
            $table->index(['affiliate_id', 'status']);
            $table->index(['referred_user_id', 'status']);
        });

        // Referral Transactions
        Schema::create('referral_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_transaction_id')->constrained()->cascadeOnDelete();
            $table->decimal('course_price', 8, 2);
            $table->decimal('platform_share', 8, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 8, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['referral_id', 'status']);
            $table->index(['course_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_transactions');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('affiliates');
    }
};
