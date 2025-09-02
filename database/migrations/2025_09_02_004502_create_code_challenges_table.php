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
       // Code Challenges
       Schema::create('code_challenges', function (Blueprint $table) {
        $table->id();
        $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description');
        $table->longText('problem_statement');
        $table->json('test_cases')->nullable();
        $table->json('sample_inputs')->nullable();
        $table->json('sample_outputs')->nullable();
        $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
        $table->json('tags')->nullable();
        $table->integer('points')->default(100);
        $table->timestamp('starts_at')->nullable();
        $table->timestamp('ends_at')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_challenges');
    }
};
