<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('estimated_duration_minutes')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->boolean('is_premium')->default(false);
            $table->boolean('has_offline_content')->default(false);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->text('target_audience')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->json('prerequisites')->nullable();
            $table->text('syllabus_overview')->nullable();
            $table->integer('total_modules')->default(0);
            $table->integer('total_projects')->default(0);
            $table->integer('total_assessments')->default(0);
            $table->json('faqs')->nullable();
            $table->string('certificate_template')->nullable();
            $table->boolean('has_projects')->default(false);
            $table->boolean('has_assessments')->default(false);
            $table->integer('completion_rate_threshold')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
}