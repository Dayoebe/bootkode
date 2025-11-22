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
        Schema::table('courses', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('courses', 'materials_included')) {
                $table->json('materials_included')->nullable()->after('external_links');
            }
            
            if (!Schema::hasColumn('courses', 'tags')) {
                $table->json('tags')->nullable()->after('materials_included');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['materials_included', 'tags']);
        });
    }
};