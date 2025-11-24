<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->boolean('shuffle_questions')->default(false)->after('max_attempts');
            $table->boolean('shuffle_options')->default(false)->after('shuffle_questions');
        });

        // Add shuffle_order column to student_answers to store the randomized order
        Schema::table('student_answers', function (Blueprint $table) {
            $table->json('question_order')->nullable()->after('attempt_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['shuffle_questions', 'shuffle_options']);
        });

        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropColumn('question_order');
        });
    }
};