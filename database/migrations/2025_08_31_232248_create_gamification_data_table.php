<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Main gamification table - stores all gaming data
        Schema::create('gamification_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Core gaming stats
            $table->integer('total_points')->default(0);
            $table->integer('level')->default(1);
            $table->integer('experience_points')->default(0);
            $table->integer('experience_to_next_level')->default(100);
            
            // Streaks and engagement
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            
            // Gaming currencies
            $table->integer('coins')->default(0);
            $table->integer('gems')->default(0);
            $table->integer('energy')->default(100);
            $table->timestamp('energy_last_updated')->nullable();
            
            // Game-specific data
            $table->json('game_scores')->nullable(); // Store scores for different games
            $table->json('unlocked_features')->nullable(); // Track unlocked content
            $table->json('daily_quests')->nullable(); // Daily challenges
            $table->json('weekly_quests')->nullable(); // Weekly challenges
            $table->timestamp('last_quest_reset')->nullable();
            
            // Social features
            $table->integer('friends_count')->default(0);
            $table->integer('challenges_won')->default(0);
            $table->integer('challenges_participated')->default(0);
            
            $table->timestamps();
            
            $table->index(['user_id', 'total_points']);
            $table->index(['level', 'experience_points']);
        });

        // Badges and achievements
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_type'); // achievement, streak, completion, special
            $table->string('badge_key'); // unique identifier
            $table->string('badge_name');
            $table->text('badge_description');
            $table->string('badge_icon');
            $table->string('badge_color')->default('#3B82F6');
            $table->string('rarity')->default('common'); // common, rare, epic, legendary
            $table->integer('points_reward')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('unlock_criteria')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'badge_key']);
            $table->index(['badge_type', 'rarity']);
        });

        // Game activities and transactions
        Schema::create('gamification_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // earn, spend, bonus, penalty
            $table->string('currency_type'); // points, coins, gems, experience
            $table->integer('amount');
            $table->string('source'); // lesson_complete, quiz_pass, daily_login, etc.
            $table->text('description');
            $table->json('metadata')->nullable(); // Additional context
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['transaction_type', 'currency_type']);
        });

        // Store purchases and rewards
        Schema::create('reward_store_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_type'); // avatar, theme, feature, boost
            $table->string('item_key')->unique();
            $table->string('name');
            $table->text('description');
            $table->string('icon');
            $table->string('image_url')->nullable();
            $table->integer('cost_coins')->default(0);
            $table->integer('cost_gems')->default(0);
            $table->integer('required_level')->default(1);
            $table->json('requirements')->nullable(); // Additional unlock requirements
            $table->boolean('is_available')->default(true);
            $table->boolean('is_limited_time')->default(false);
            $table->timestamp('available_until')->nullable();
            $table->json('item_data')->nullable(); // Item-specific configuration
            $table->timestamps();
        });

        // User's purchased items
        Schema::create('user_store_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_store_item_id')->constrained()->onDelete('cascade');
            $table->boolean('is_equipped')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'reward_store_item_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_store_purchases');
        Schema::dropIfExists('reward_store_items');
        Schema::dropIfExists('gamification_transactions');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('gamification_data');
    }
};