<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('course_user', function (Blueprint $table) {
            if (!Schema::hasColumn('course_user', 'progress')) {
                $table->decimal('progress', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('course_user', 'last_accessed_at')) {
                $table->timestamp('last_accessed_at')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'time_spent_minutes')) {
                $table->integer('time_spent_minutes')->default(0);
            }
            
            // Add indexes if they don't exist
            // $table->unique(['course_id', 'user_id']);
            // $table->index(['user_id', 'progress']);
            // $table->index(['user_id', 'last_accessed_at']);
        });
    }

    public function down()
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropColumn(['progress', 'last_accessed_at', 'completed_at', 'time_spent_minutes']);
        });
    }
};