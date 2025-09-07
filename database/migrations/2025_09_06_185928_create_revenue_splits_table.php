<?php

// Migration 5: create_revenue_splits_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('revenue_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('instructor_percentage', 5, 2)->default(80.00);
            $table->decimal('platform_percentage', 5, 2)->default(20.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['course_id']);
            $table->index(['instructor_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('revenue_splits');
    }
};
