<?php
// Create migration: php artisan make:migration create_marketplace_categories_table
// database/migrations/xxxx_xx_xx_create_marketplace_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marketplace_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->default('fas fa-folder');
            $table->string('color', 7)->default('#6366f1'); // Hex color
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('metadata')->nullable(); // For future extensibility
            $table->timestamps();
            
            // Indexes
            $table->index(['is_active', 'sort_order']);
            $table->index('is_featured');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketplace_categories');
    }
};
