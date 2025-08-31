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
             Schema::create('cbt_exam_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cbt_exam_id')->constrained()->cascadeOnDelete();
                $table->foreignId('question_id')->constrained()->cascadeOnDelete();
                $table->integer('order')->default(0);
                $table->decimal('points', 8, 2)->default(1.00);
                $table->boolean('is_mandatory')->default(false);
                $table->timestamps();
                
                $table->unique(['cbt_exam_id', 'question_id']);
                $table->index(['cbt_exam_id', 'order']);
            });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_exam_questions');
    }
};
