<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('response_count')->default(0);
            $table->decimal('response_rate', 5, 2)->default(0);
            $table->decimal('sentiment_score', 3, 2)->nullable(); // -1 to 1
            $table->json('keyword_frequencies')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_analytics');
    }
};