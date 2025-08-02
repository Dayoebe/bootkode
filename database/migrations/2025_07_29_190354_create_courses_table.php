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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade'); // Link to the user who is the instructor
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->onDelete('set null'); // Link to course category
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable(); // URL or path to course thumbnail image
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('estimated_duration_minutes')->nullable(); // Estimated time to complete the course
            $table->decimal('price', 8, 2)->default(0.00); // Price for premium courses, 0 for free
            $table->boolean('is_premium')->default(false); // Whether the course requires payment
            $table->boolean('is_published')->default(false); // Whether the course is visible to students
            $table->string('status')->default('draft');
            $table->boolean('is_approved')->default(false); // For admin approval workflow
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};