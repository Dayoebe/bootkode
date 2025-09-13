<?php
// Create migration: php artisan make:migration create_marketplace_item_categories_table
// database/migrations/xxxx_xx_xx_create_marketplace_item_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('marketplace_item_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('marketplace_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate relationships
            $table->unique(['marketplace_item_id', 'marketplace_category_id'], 'item_category_unique');
            
            // Indexes for performance
            $table->index('marketplace_item_id');
            $table->index('marketplace_category_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketplace_item_categories');
    }
};