<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('achievement_type');
            $table->string('achievement_name');
            $table->string('achievement_description');
            $table->string('achievement_icon')->nullable();
            $table->integer('achievement_value')->default(0);
            $table->timestamp('earned_at');
            $table->timestamps();
            
            $table->index(['user_id', 'achievement_type']);
            $table->index(['user_id', 'earned_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_achievements');
    }
};