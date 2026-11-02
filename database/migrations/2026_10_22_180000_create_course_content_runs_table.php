<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_content_runs', function (Blueprint $table) {
            $table->id();
            $table->string('source_key', 64);
            $table->string('revision', 64)->nullable();
            $table->string('source_type', 24);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 24)->index();
            $table->json('metadata')->nullable();
            $table->json('stage_data')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->unique(['source_key', 'revision']);
            $table->index(['user_id', 'created_at']);
            $table->index(['course_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_content_runs');
    }
};
