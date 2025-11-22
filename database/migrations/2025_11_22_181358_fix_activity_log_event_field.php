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
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Make event nullable or add default value
            if (Schema::hasColumn('activity_log', 'event')) {
                $table->string('event')->nullable()->default('custom')->change();
            } else {
                $table->string('event')->nullable()->default('custom')->after('log_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Optionally revert the change
            if (Schema::hasColumn('activity_log', 'event')) {
                $table->string('event')->nullable(false)->change();
            }
        });
    }
};