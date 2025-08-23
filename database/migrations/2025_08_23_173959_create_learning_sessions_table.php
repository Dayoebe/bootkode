<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('learning_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->string('activity_type')->default('lesson');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'started_at']);
            $table->index(['user_id', 'activity_type']);
            $table->index(['course_id', 'started_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('learning_sessions');
    }
};