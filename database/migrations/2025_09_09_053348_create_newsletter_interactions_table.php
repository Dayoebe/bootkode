<?php

// MIGRATION 3: newsletter_interactions.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('newsletter_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('newsletter_campaigns')->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->onDelete('cascade');
            $table->string('type'); // send, open, click, bounce, unsubscribe
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->json('data')->nullable(); // Store URL for clicks, error for bounces, etc.
            $table->string('tracking_token', 64)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'subscriber_id', 'type']);
            $table->index(['type', 'created_at']);
            $table->index('tracking_token');
        });

        // Insert default settings as JSON in a single row
        DB::table('newsletter_campaigns')->insert([
            'name' => '_system_settings',
            'subject' => 'System Settings',
            'html_content' => '',
            'from_name' => 'System',
            'from_email' => 'system@bootkode.com',
            'type' => 'settings',
            'status' => 'active',
            'variables' => json_encode([
                'default_from_name' => 'Bootkode Academy',
                'default_from_email' => 'wirelesstexter@gmail.com',
                'throttle_limit' => 100,
                'throttle_delay' => 60,
                'unsubscribe_page_content' => [
                    'title' => 'Unsubscribe Confirmation',
                    'message' => 'You have been successfully unsubscribed from our newsletter.',
                    'resubscribe_text' => 'Changed your mind? You can resubscribe anytime.'
                ]
            ]),
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('newsletter_interactions');
    }
};
