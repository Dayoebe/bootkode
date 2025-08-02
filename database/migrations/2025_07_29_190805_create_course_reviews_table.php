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
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Reviewer
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade'); // Course being reviewed
            $table->integer('rating')->unsigned()->default(1); // Rating from 1 to 5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']); // A user can only review a course once
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_reviews');
    }
};