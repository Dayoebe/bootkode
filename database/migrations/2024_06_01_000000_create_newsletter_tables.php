<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Newsletter Subscribers
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('status')->default('active');
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->string('source')->default('website');
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('unsubscribe_token', 64)->unique()->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // Newsletter Campaigns
        Schema::create('newsletter_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('preview_text')->nullable();
            $table->longText('html_content');
            $table->string('from_name');
            $table->string('from_email');
            $table->string('reply_to')->nullable();
            $table->string('status')->default('draft');
            $table->string('type')->default('campaign');
            
            $table->json('recipient_filters')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->integer('bounce_count')->default(0);
            $table->integer('unsubscribe_count')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            
            $table->text('description')->nullable();
            $table->json('variables')->nullable();
            $table->boolean('is_default')->default(false);
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['status', 'type']);
            $table->index(['type', 'is_default']);
        });

        // Newsletter Interactions
        Schema::create('newsletter_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('newsletter_campaigns')->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->onDelete('cascade');
            $table->string('type');
            $table->string('status')->default('pending');
            $table->json('data')->nullable();
            $table->string('tracking_token', 64)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'subscriber_id', 'type']);
            $table->index(['type', 'created_at']);
            $table->index('tracking_token');
        });

        // Insert default settings only if a user exists
        $adminUser = DB::table('users')->first();
        if ($adminUser) {
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
                'created_by' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_interactions');
        Schema::dropIfExists('newsletter_campaigns');
        Schema::dropIfExists('newsletter_subscribers');
    }
};