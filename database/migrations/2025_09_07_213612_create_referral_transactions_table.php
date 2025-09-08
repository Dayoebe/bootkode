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
          // Referral transactions - detailed commission tracking per transaction
          Schema::create('referral_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_transaction_id')->constrained()->cascadeOnDelete();
            $table->decimal('course_price', 8, 2);
            $table->decimal('platform_share', 8, 2); // 20% of course price
            $table->decimal('commission_rate', 5, 2); // % of platform share
            $table->decimal('commission_amount', 8, 2); // Final commission paid
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['referral_id', 'status']);
            $table->index(['course_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_transactions');
    }
};
