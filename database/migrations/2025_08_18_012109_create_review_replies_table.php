<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewRepliesTable extends Migration
{
    public function up()
    {
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('course_reviews')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('reply_text');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_replies');
    }
}