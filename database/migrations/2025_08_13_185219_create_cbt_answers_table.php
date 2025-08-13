<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCbtAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('cbt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('cbt_question_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('selected_option_index')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cbt_answers');
    }
}