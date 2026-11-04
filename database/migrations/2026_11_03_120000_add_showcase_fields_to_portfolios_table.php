<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table): void {
            $table->string('source_code_url', 2048)->nullable()->after('project_url');
            $table->enum('visibility', ['private', 'public'])->default('private')->after('is_public');
            $table->index(['user_id', 'visibility', 'sort_order'], 'portfolios_owner_visibility_order_index');
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table): void {
            $table->dropIndex('portfolios_owner_visibility_order_index');
            $table->dropColumn(['source_code_url', 'visibility']);
        });
    }
};
