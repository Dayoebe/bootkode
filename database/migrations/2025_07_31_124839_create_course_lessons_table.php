<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function __construct()
{
    $this->connection = config('database.default');
    $this->withinTransaction = false;
}
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained('course_sections')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique(); // A unique slug for the lesson
            $table->longText('content')->nullable(); // For text-based lessons
            $table->string('video_url')->nullable(); // For video lessons
            $table->integer('order')->default(0); // For ordering lessons within a section
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};