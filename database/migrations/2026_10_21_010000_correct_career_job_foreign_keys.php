<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Preflight every table before any DDL; never discard unmatched legacy rows.
        foreach (['job_applications', 'job_saves'] as $table) {
            if (DB::table($table)->leftJoin('job_portal', "$table.job_id", '=', 'job_portal.id')
                ->whereNull('job_portal.id')->exists()) {
                throw new RuntimeException("Unmatched career records in {$table}; reconcile them before migrating. No records were changed.");
            }
        }

        foreach (['job_applications', 'job_saves'] as $table) {
            $foreign = collect(Schema::getForeignKeys($table))->firstWhere('columns', ['job_id']);
            if (($foreign['foreign_table'] ?? null) === 'job_portal') {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) use ($foreign) {
                if ($foreign) {
                    $blueprint->dropForeign($foreign['name']);
                }
                $blueprint->foreign('job_id')->references('id')->on('job_portal')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Forward-only compatibility repair: never restore the queue-table relation.
    }
};
