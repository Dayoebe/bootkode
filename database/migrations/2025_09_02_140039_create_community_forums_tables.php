<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Forum Categories
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('fas fa-folder');
            $table->string('color')->default('#3B82F6');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Forum Threads
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('forum_categories')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('views')->default(0);
            $table->integer('replies_count')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->foreignId('last_reply_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Forum Replies
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_threads');
        Schema::dropIfExists('forum_categories');
    }
};