<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('reviewable'); // This automatically creates an index
            $table->integer('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_featured')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Remove this line as morphs() already creates the index:
            // $table->index(['reviewable_type', 'reviewable_id']);
            
            // Keep these indexes
            $table->index('is_approved');
            $table->index('is_featured');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_reviews');
    }
};