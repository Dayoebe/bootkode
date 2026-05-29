<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'quality_score')) {
                $table->unsignedTinyInteger('quality_score')->default(0)->after('average_rating');
            }

            if (! Schema::hasColumn('courses', 'quality_status')) {
                $table->string('quality_status', 30)->default('not_checked')->after('quality_score')->index();
            }

            if (! Schema::hasColumn('courses', 'quality_public_label')) {
                $table->string('quality_public_label')->nullable()->after('quality_status');
            }

            if (! Schema::hasColumn('courses', 'quality_public_label_enabled')) {
                $table->boolean('quality_public_label_enabled')->default(true)->after('quality_public_label');
            }

            if (! Schema::hasColumn('courses', 'quality_summary')) {
                $table->json('quality_summary')->nullable()->after('quality_public_label_enabled');
            }

            if (! Schema::hasColumn('courses', 'quality_issues')) {
                $table->json('quality_issues')->nullable()->after('quality_summary');
            }

            if (! Schema::hasColumn('courses', 'quality_last_checked_at')) {
                $table->timestamp('quality_last_checked_at')->nullable()->after('quality_issues')->index();
            }

            if (! Schema::hasColumn('courses', 'quality_reviewed_at')) {
                $table->timestamp('quality_reviewed_at')->nullable()->after('quality_last_checked_at');
            }

            if (! Schema::hasColumn('courses', 'quality_review_due_at')) {
                $table->timestamp('quality_review_due_at')->nullable()->after('quality_reviewed_at')->index();
            }

            if (! Schema::hasColumn('courses', 'quality_checked_by')) {
                $table->foreignId('quality_checked_by')->nullable()->after('quality_review_due_at')->constrained('users')->nullOnDelete();
            }
        });

        if (! Schema::hasTable('course_quality_checks')) {
            Schema::create('course_quality_checks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedTinyInteger('score')->default(0);
                $table->string('status', 30)->default('not_checked')->index();
                $table->string('public_label')->nullable();
                $table->unsignedTinyInteger('completeness_percent')->default(0);
                $table->unsignedTinyInteger('assessment_coverage_percent')->default(0);
                $table->unsignedTinyInteger('media_health_percent')->default(0);
                $table->unsignedTinyInteger('freshness_percent')->default(0);
                $table->unsignedInteger('broken_media_count')->default(0);
                $table->unsignedInteger('unchecked_external_media_count')->default(0);
                $table->boolean('remote_media_checked')->default(false);
                $table->json('issues')->nullable();
                $table->json('media_results')->nullable();
                $table->json('summary')->nullable();
                $table->timestamp('checked_at')->nullable()->index();
                $table->timestamps();

                $table->index(['course_id', 'checked_at']);
                $table->index(['status', 'score']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_quality_checks');

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'quality_checked_by')) {
                $table->dropConstrainedForeignId('quality_checked_by');
            }

            foreach ([
                'quality_review_due_at',
                'quality_reviewed_at',
                'quality_last_checked_at',
                'quality_issues',
                'quality_summary',
                'quality_public_label_enabled',
                'quality_public_label',
                'quality_status',
                'quality_score',
            ] as $column) {
                if (Schema::hasColumn('courses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
