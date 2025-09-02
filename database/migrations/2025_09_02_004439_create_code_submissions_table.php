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
    { // Code Challenge Submissions
        Schema::create('code_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('code_challenges')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->longText('code');
            $table->string('language');
            $table->enum('status', ['pending', 'passed', 'failed'])->default('pending');
            $table->text('feedback')->nullable();
            $table->integer('score')->default(0);
            $table->timestamp('submitted_at')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_submissions');
    }
};
