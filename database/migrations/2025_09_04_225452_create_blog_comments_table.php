<?php

// Migration 3: create_blog_comments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('author_name')->nullable(); // for guest comments
            $table->string('author_email')->nullable(); // for guest comments
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();
            
            $table->foreign('post_id')->references('id')->on('blog_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('blog_comments')->onDelete('cascade');
            $table->index(['post_id', 'status', 'created_at']);
            $table->index('parent_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_comments');
    }
};