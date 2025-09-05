<?php

// Migration 5: create_blog_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blog_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, boolean, integer, json
            $table->string('group')->default('general');
            $table->timestamps();
            
            $table->index('group');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_settings');
    }
};
