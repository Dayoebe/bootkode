<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_notification_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('event_key', 64);
            $table->string('family', 40);
            $table->uuid('notification_id');
            $table->timestamp('created_at');
            $table->unique(['user_id', 'event_key']);
        });
    }

    public function down(): void
    {
        throw new LogicException('Learning delivery receipts require a backed-up reviewed forward change.');
    }
};
