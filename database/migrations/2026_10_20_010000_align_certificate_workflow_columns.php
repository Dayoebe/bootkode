<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Retain every existing status while supporting requested and rejected.
        if (Schema::getColumnType('certificates', 'status') === 'enum') {
            Schema::table('certificates', fn (Blueprint $table) => $table->string('status', 32)->default('pending')->change());
        }

        foreach (['verification_code', 'verification_url', 'grade', 'certificate_template', 'qr_code_path', 'pdf_path'] as $column) {
            if (! Schema::hasColumn('certificates', $column)) {
                Schema::table('certificates', function (Blueprint $table) use ($column) {
                    $field = $table->string($column)->nullable();
                    if ($column === 'verification_code') $field->unique();
                });
            }
        }
        foreach (['requested_at', 'rejected_at'] as $column) {
            if (! Schema::hasColumn('certificates', $column)) {
                Schema::table('certificates', fn (Blueprint $table) => $table->timestamp($column)->nullable());
            }
        }
        if (! Schema::hasColumn('certificates', 'rejected_by')) {
            Schema::table('certificates', fn (Blueprint $table) => $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete());
        }
        if (! Schema::hasColumn('certificates', 'rejection_reason')) {
            Schema::table('certificates', fn (Blueprint $table) => $table->text('rejection_reason')->nullable());
        }
        if (! Schema::hasColumn('certificates', 'credits')) {
            Schema::table('certificates', fn (Blueprint $table) => $table->decimal('credits', 8, 2)->nullable());
        }

        // Existing public tokens/files remain authoritative; never replace them.
        DB::table('certificates')->whereNull('verification_code')->whereNotNull('verification_token')
            ->update(['verification_code' => DB::raw('verification_token')]);
        DB::table('certificates')->whereNull('requested_at')->update(['requested_at' => DB::raw('created_at')]);
        DB::table('certificates')->whereNull('pdf_path')->whereNotNull('certificate_file')
            ->update(['pdf_path' => DB::raw('certificate_file')]);
    }

    public function down(): void
    {
        // Forward-only compatibility migration: retaining columns preserves issued credentials.
    }
};
