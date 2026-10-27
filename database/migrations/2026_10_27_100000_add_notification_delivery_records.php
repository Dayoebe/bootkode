<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The existing JobAlert model had no backing migration. Existing ad-hoc tables need review.
        Schema::create('job_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 100);
            $table->json('keywords')->nullable();
            $table->string('location')->nullable();
            $table->string('category')->nullable();
            $table->string('employment_type', 30)->nullable();
            $table->string('work_type', 20)->nullable();
            $table->string('experience_level', 30)->nullable();
            $table->decimal('salary_min', 14, 2)->nullable();
            $table->decimal('salary_max', 14, 2)->nullable();
            $table->char('salary_currency', 3)->nullable();
            $table->char('filter_hash', 64);
            $table->boolean('is_active')->default(true);
            $table->string('frequency', 10)->default('daily');
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'filter_hash']);
            $table->index(['is_active', 'last_sent_at']);
        });
        Schema::create('job_alert_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_alert_id')->constrained('job_alerts')->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('job_portal')->cascadeOnDelete();
            $table->timestamp('created_at');
            $table->unique(['job_alert_id', 'job_id']);
        });
        Schema::table('community_feedback', fn (Blueprint $table) => $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete());
    }

    public function down(): void
    {
        throw new LogicException('Notification records require a reviewed backed-up forward change; automatic rollback could erase saved alerts or response history.');
    }
};
