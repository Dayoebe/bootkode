<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
public function up()
{
    Schema::table('student_answers', function (Blueprint $table) {
        $table->json('exam_data')->nullable()->after('question_order');
    });
}

public function down()
{
    Schema::table('student_answers', function (Blueprint $table) {
        $table->dropColumn('exam_data');
    });
}};