<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gamification Data
        Schema::create('gamification_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->integer('total_points')->default(0);
            $table->integer('level')->default(1);
            $table->integer('experience_points')->default(0);
            $table->integer('experience_to_next_level')->default(100);
            
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            
            $table->integer('coins')->default(0);
            $table->integer('gems')->default(0);
            $table->integer('energy')->default(100);
            $table->timestamp('energy_last_updated')->nullable();
            
            $table->json('game_scores')->nullable();
            $table->json('unlocked_features')->nullable();
            $table->json('daily_quests')->nullable();
            $table->json('weekly_quests')->nullable();
            $table->timestamp('last_quest_reset')->nullable();
            
            $table->integer('friends_count')->default(0);
            $table->integer('challenges_won')->default(0);
            $table->integer('challenges_participated')->default(0);
            
            $table->timestamps();
            
            $table->index(['user_id', 'total_points']);
            $table->index(['level', 'experience_points']);
        });

        // User Badges
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_type');
            $table->string('badge_key');
            $table->string('badge_name');
            $table->text('badge_description');
            $table->string('badge_icon');
            $table->string('badge_color')->default('#3B82F6');
            $table->string('rarity')->default('common');
            $table->integer('points_reward')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('unlock_criteria')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'badge_key']);
            $table->index(['badge_type', 'rarity']);
        });

        // Gamification Transactions
        Schema::create('gamification_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type');
            $table->string('currency_type');
            $table->integer('amount');
            $table->string('source');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['transaction_type', 'currency_type']);
        });

        // Reward Store Items
        Schema::create('reward_store_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_type');
            $table->string('item_key')->unique();
            $table->string('name');
            $table->text('description');
            $table->string('icon');
            $table->string('image_url')->nullable();
            $table->integer('cost_coins')->default(0);
            $table->integer('cost_gems')->default(0);
            $table->integer('required_level')->default(1);
            $table->json('requirements')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_limited_time')->default(false);
            $table->timestamp('available_until')->nullable();
            $table->json('item_data')->nullable();
            $table->timestamps();
        });

        // User Store Purchases
        Schema::create('user_store_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_store_item_id')->constrained()->onDelete('cascade');
            $table->boolean('is_equipped')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'reward_store_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_store_purchases');
        Schema::dropIfExists('reward_store_items');
        Schema::dropIfExists('gamification_transactions');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('gamification_data');
    }
};
