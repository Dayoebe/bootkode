<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('code_reviews')) {
            if (Schema::hasColumn('code_reviews', 'code_quality_score') && DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE code_reviews MODIFY code_quality_score DECIMAL(5,2) NULL');
            }

            Schema::table('code_reviews', function (Blueprint $table) {
                if (! Schema::hasColumn('code_reviews', 'submission_type')) {
                    $table->string('submission_type')->default('repository')->after('priority');
                }

                if (! Schema::hasColumn('code_reviews', 'language')) {
                    $table->string('language')->nullable()->after('code_snippets');
                }

                if (! Schema::hasColumn('code_reviews', 'commit_hash')) {
                    $table->string('commit_hash')->nullable()->after('branch_name');
                }

                if (! Schema::hasColumn('code_reviews', 'learner_goal')) {
                    $table->text('learner_goal')->nullable()->after('specific_questions');
                }

                if (! Schema::hasColumn('code_reviews', 'rubric_scores')) {
                    $table->json('rubric_scores')->nullable()->after('improvement_areas');
                }

                if (! Schema::hasColumn('code_reviews', 'rubric_notes')) {
                    $table->json('rubric_notes')->nullable()->after('rubric_scores');
                }

                if (! Schema::hasColumn('code_reviews', 'rubric_total_score')) {
                    $table->decimal('rubric_total_score', 5, 2)->nullable()->after('rubric_notes');
                }

                if (! Schema::hasColumn('code_reviews', 'approval_status')) {
                    $table->string('approval_status')->default('pending')->after('rubric_total_score');
                }

                if (! Schema::hasColumn('code_reviews', 'approval_notes')) {
                    $table->text('approval_notes')->nullable()->after('approval_status');
                }

                if (! Schema::hasColumn('code_reviews', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('approval_notes');
                }

                if (! Schema::hasColumn('code_reviews', 'approved_by')) {
                    $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('code_reviews', 'revision_count')) {
                    $table->unsignedInteger('revision_count')->default(0)->after('approved_by');
                }

                if (! Schema::hasColumn('code_reviews', 'last_revision_at')) {
                    $table->timestamp('last_revision_at')->nullable()->after('revision_count');
                }

                if (! Schema::hasColumn('code_reviews', 'certificate_evidence')) {
                    $table->json('certificate_evidence')->nullable()->after('last_revision_at');
                }

                if (! Schema::hasColumn('code_reviews', 'project_evidence')) {
                    $table->json('project_evidence')->nullable()->after('certificate_evidence');
                }
            });
        }

        if (! Schema::hasTable('code_review_revisions')) {
            Schema::create('code_review_revisions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('code_review_id')->constrained('code_reviews')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedInteger('revision_number');
                $table->string('repository_url')->nullable();
                $table->string('branch_name')->nullable();
                $table->string('pull_request_url')->nullable();
                $table->string('commit_hash')->nullable();
                $table->json('files_to_review')->nullable();
                $table->string('language')->nullable();
                $table->longText('code_snippet')->nullable();
                $table->text('learner_goal')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();

                $table->unique(['code_review_id', 'revision_number']);
                $table->index(['code_review_id', 'submitted_at']);
            });
        }

        if (! Schema::hasTable('code_review_comments')) {
            Schema::create('code_review_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('code_review_id')->constrained('code_reviews')->cascadeOnDelete();
                $table->foreignId('code_review_revision_id')->nullable()->constrained('code_review_revisions')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type')->default('comment');
                $table->string('rubric_key')->nullable();
                $table->string('visibility')->default('participants');
                $table->text('body');
                $table->json('metadata')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->index(['code_review_id', 'type']);
                $table->index(['code_review_revision_id', 'created_at'], 'cr_comments_revision_created_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('code_review_comments');
        Schema::dropIfExists('code_review_revisions');

        if (! Schema::hasTable('code_reviews')) {
            return;
        }

        if (Schema::hasColumn('code_reviews', 'approved_by')) {
            Schema::table('code_reviews', function (Blueprint $table) {
                $table->dropForeign(['approved_by']);
            });
        }

        Schema::table('code_reviews', function (Blueprint $table) {
            foreach ([
                'submission_type',
                'language',
                'commit_hash',
                'learner_goal',
                'rubric_scores',
                'rubric_notes',
                'rubric_total_score',
                'approval_status',
                'approval_notes',
                'approved_by',
                'approved_at',
                'revision_count',
                'last_revision_at',
                'certificate_evidence',
                'project_evidence',
            ] as $column) {
                if (Schema::hasColumn('code_reviews', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
