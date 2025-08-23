<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lesson_user', function (Blueprint $table) {
            if (!Schema::hasColumn('lesson_user', 'time_spent_minutes')) {
                $table->integer('time_spent_minutes')->default(0);
            }
            
            // Add index if it doesn't exist
            $table->index(['user_id', 'completed_at']);
        });
    }

    public function down()
    {
        Schema::table('lesson_user', function (Blueprint $table) {
            $table->dropColumn(['time_spent_minutes']);
        });
    }
};