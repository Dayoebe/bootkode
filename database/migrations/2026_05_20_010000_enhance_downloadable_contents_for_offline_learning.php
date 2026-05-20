<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('downloadable_contents', function (Blueprint $table) {
            if (! Schema::hasColumn('downloadable_contents', 'status')) {
                $table->string('status')->default('ready')->after('content_types');
            }

            if (! Schema::hasColumn('downloadable_contents', 'storage_limit_mb')) {
                $table->unsignedInteger('storage_limit_mb')->default(500)->after('size_mb');
            }

            if (! Schema::hasColumn('downloadable_contents', 'storage_bytes')) {
                $table->unsignedBigInteger('storage_bytes')->default(0)->after('storage_limit_mb');
            }

            if (! Schema::hasColumn('downloadable_contents', 'cached_asset_count')) {
                $table->unsignedInteger('cached_asset_count')->default(0)->after('storage_bytes');
            }

            if (! Schema::hasColumn('downloadable_contents', 'manifest')) {
                $table->json('manifest')->nullable()->after('cached_asset_count');
            }

            if (! Schema::hasColumn('downloadable_contents', 'last_accessed_at')) {
                $table->timestamp('last_accessed_at')->nullable()->after('downloaded_at');
            }

            if (! Schema::hasColumn('downloadable_contents', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('last_accessed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('downloadable_contents', function (Blueprint $table) {
            foreach ([
                'last_synced_at',
                'last_accessed_at',
                'manifest',
                'cached_asset_count',
                'storage_bytes',
                'storage_limit_mb',
                'status',
            ] as $column) {
                if (Schema::hasColumn('downloadable_contents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
