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
        // Live Events
        Schema::create('live_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('event_type')->default('webinar'); // webinar, workshop, discussion
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->string('meeting_link')->nullable();
            $table->string('meeting_password')->nullable();
            $table->integer('max_attendees')->nullable();
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->json('resources')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_events');
    }
};
