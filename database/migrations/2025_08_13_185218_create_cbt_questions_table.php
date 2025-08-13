<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCbtQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('cbt_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->json('options'); // ['Option A', 'Option B', 'Option C', 'Option D']
            $table->integer('correct_option_index');
            $table->integer('marks');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cbt_questions');
    }
}