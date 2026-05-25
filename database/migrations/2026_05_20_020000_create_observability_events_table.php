<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('observability_events')) {
            return;
        }

        Schema::create('observability_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index();
            $table->string('severity', 20)->default('warning')->index();
            $table->string('status', 20)->default('open')->index();
            $table->string('source', 120)->nullable()->index();
            $table->string('summary');
            $table->longText('message')->nullable();
            $table->string('url', 2048)->nullable();
            $table->string('method', 12)->nullable();
            $table->string('route_name')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('fingerprint', 64)->index();
            $table->unsignedInteger('occurrences')->default(1);
            $table->json('context')->nullable();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'status', 'last_seen_at']);
            $table->index(['severity', 'status', 'last_seen_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observability_events');
    }
};
