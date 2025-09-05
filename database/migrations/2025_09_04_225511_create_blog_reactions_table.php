<?php

// Migration 4: create_blog_reactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blog_reactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('reactable'); // for posts and comments
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address')->nullable(); // for guest reactions
            $table->enum('type', ['like', 'bookmark'])->default('like');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['reactable_type', 'reactable_id', 'user_id', 'ip_address', 'type'], 'unique_reaction');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_reactions');
    }
};
