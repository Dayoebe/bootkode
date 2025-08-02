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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade'); // Lesson belongs to a module
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'pdf', 'audio', 'text'])->default('text'); // Type of lesson content
            $table->string('content_url')->nullable(); // URL or path to the lesson file (video, pdf, audio)
            $table->text('text_content')->nullable(); // For text-based lessons
            $table->integer('duration_minutes')->nullable(); // For video/audio lessons
            $table->integer('order')->default(0); // Order of lessons within a module
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};