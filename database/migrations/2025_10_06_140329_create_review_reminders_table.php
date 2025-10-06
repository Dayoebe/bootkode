<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('reminder_count')->default(0);
            $table->timestamp('last_reminded_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('unsubscribed')->default(false);
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index('last_reminded_at');
        });

        // Add settings column to users table for reminder preferences
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'review_reminder_preferences')) {
                $table->json('review_reminder_preferences')->nullable()->after('notification_preferences');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_reminders');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('review_reminder_preferences');
        });
    }
};