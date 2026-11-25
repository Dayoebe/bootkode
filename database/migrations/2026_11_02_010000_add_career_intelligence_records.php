<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_portal', function (Blueprint $table) {
            $table->json('competency_requirements')->nullable();
            $table->string('requirements_source_hash', 64)->nullable();
            $table->foreignId('requirements_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('requirements_reviewed_at')->nullable();
            $table->json('requirements_review_history')->nullable();
        });
        Schema::create('competency_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kind', 24);
            $table->string('label', 120);
            $table->string('claim_hash', 64);
            $table->string('source_type', 24);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('reference');
            $table->decimal('years_experience', 5, 2)->nullable();
            $table->string('review_status', 24)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('review_reason')->nullable();
            $table->json('review_history')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'claim_hash']);
            $table->index(['user_id', 'review_status']);
            $table->index(['review_status', 'created_at']);
        });
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::table('competency_evidence')->exists()
            || \Illuminate\Support\Facades\DB::table('job_portal')->whereNotNull('requirements_reviewed_at')->exists()) {
            throw new \RuntimeException('Career evidence or requirement reviews exist. Preserve their history with a forward compatibility migration.');
        }
        Schema::dropIfExists('competency_evidence');
        Schema::table('job_portal', function (Blueprint $table) {
            $table->dropForeign(['requirements_reviewed_by']);
            $table->dropColumn(['competency_requirements', 'requirements_source_hash', 'requirements_reviewed_by', 'requirements_reviewed_at', 'requirements_review_history']);
        });
    }
};
