<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseRejectionsTable extends Migration
{
    public function up()
    {
        Schema::create('course_rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('reason');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_rejections');
    }
}