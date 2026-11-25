<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_learning_profiles', function (Blueprint $table) {
            $table->json('interests')->nullable();
            $table->json('existing_knowledge')->nullable();
            $table->string('starting_level', 32)->nullable();
            $table->json('learning_preferences')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->timestamp('onboarding_skipped_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ai_learning_profiles', function (Blueprint $table) {
            $table->dropColumn(['interests', 'existing_knowledge', 'starting_level', 'learning_preferences', 'onboarding_completed_at', 'onboarding_skipped_at']);
        });
    }
};
