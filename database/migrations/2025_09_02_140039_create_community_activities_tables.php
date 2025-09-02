<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Community Activities (Study Groups, Code Challenges, Live Events)
        Schema::create('community_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['study_group', 'code_challenge', 'live_event']);
            $table->string('title');
            $table->text('description');
            $table->json('tags')->nullable();
            $table->integer('max_participants')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->string('location')->nullable(); // For live events (URL or physical location)
            $table->text('requirements')->nullable(); // For code challenges
            $table->json('metadata')->nullable(); // Flexible data storage
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->integer('participants_count')->default(0);
            $table->timestamps();
        });

        // Activity Participants
        Schema::create('activity_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('community_activities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['joined', 'pending', 'completed', 'left'])->default('joined');
            $table->json('submission_data')->nullable(); // For code challenge submissions
            $table->integer('score')->nullable(); // For challenges/competitions
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['activity_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_participants');
        Schema::dropIfExists('community_activities');
    }
};