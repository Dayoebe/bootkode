<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('student_answers', 'attempt_number')) {
                $table->integer('attempt_number')->default(1);
            }
            if (!Schema::hasColumn('student_answers', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable();
            }
            if (!Schema::hasColumn('student_answers', 'time_spent_seconds')) {
                $table->integer('time_spent_seconds')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropColumn(['attempt_number', 'submitted_at', 'time_spent_seconds']);
        });
    }
};