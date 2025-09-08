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
               // Referrals table - tracks referred users and their activity
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
                $table->json('metadata')->nullable(); // Track sources, campaigns, etc.
                $table->timestamps();
    
                $table->unique(['affiliate_id', 'referred_user_id']);
                $table->index(['affiliate_id', 'status']);
                $table->index(['referred_user_id', 'status']);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
