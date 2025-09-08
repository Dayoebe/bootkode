<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Affiliates table - stores affiliate account information
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('referral_code', 20)->unique();
            $table->decimal('commission_rate', 5, 2)->default(30.00); // 30% of platform share
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->decimal('total_earned', 10, 2)->default(0);
            $table->integer('total_referrals')->default(0);
            $table->integer('active_referrals')->default(0); // Users who made at least one purchase
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->json('metadata')->nullable(); // Store additional data like social links, etc.
            $table->timestamps();

            $table->index(['status', 'commission_rate']);
            $table->index('referral_code');
        });

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
