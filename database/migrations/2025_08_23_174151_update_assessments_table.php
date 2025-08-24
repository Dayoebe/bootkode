<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'max_score')) {
                $table->integer('max_score')->default(100);
            }
            
            // Add indexes if they don't exist
            // $table->index(['course_id', 'type']);
            // $table->index(['lesson_id']);
        });
    }

    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['max_score']);
        });
    }
};