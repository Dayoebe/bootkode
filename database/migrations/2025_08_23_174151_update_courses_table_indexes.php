<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // $table->dropIndex('courses_category_id_difficulty_level_index'); // avoid duplicates
            // $table->dropIndex('courses_is_published_is_approved_index');    // avoid duplicates
    
            $table->index(['category_id', 'difficulty_level'], 'courses_category_id_difficulty_level_index');
            $table->index(['is_published', 'is_approved'], 'courses_is_published_is_approved_index');
        });
    }
    

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_category_id_difficulty_level_index');
            $table->dropIndex('courses_is_published_is_approved_index');
        });
    }
};
