<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCbtResultsTable extends Migration
{
    public function up()
    {
        Schema::create('cbt_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->integer('total_marks');
            $table->boolean('passed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cbt_results');
    }
}