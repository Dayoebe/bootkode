<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentsTable extends Migration
{
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedInteger('order')->default(0);
            $table->text('description')->nullable();
            $table->enum('type', ['quiz', 'project', 'assignment'])->default('quiz');
            $table->integer('pass_percentage')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->datetime('deadline')->nullable();
            $table->string('project_type')->nullable();
            $table->json('required_skills')->nullable();
            $table->json('deliverables')->nullable();
            $table->json('resources')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->integer('weight')->nullable();
            $table->boolean('allows_collaboration')->default(false);
            $table->text('evaluation_criteria')->nullable();
            $table->datetime('due_date')->nullable();
            $table->integer('max_score')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessments');
    }
}