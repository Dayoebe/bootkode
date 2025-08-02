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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->onDelete('cascade'); // Quiz can be tied to a specific lesson
            $table->foreignId('module_id')->nullable()->constrained('modules')->onDelete('cascade'); // Or to an entire module
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('pass_percentage')->default(70); // Minimum score to pass
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};