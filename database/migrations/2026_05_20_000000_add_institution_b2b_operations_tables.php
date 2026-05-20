<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email');
            $table->enum('role', ['admin', 'instructor', 'student', 'observer'])->default('admin');
            $table->string('department')->nullable();
            $table->string('token')->unique();
            $table->enum('status', ['pending', 'accepted', 'revoked', 'expired'])->default('pending');
            $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('accepted_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index(['email', 'status']);
        });

        Schema::create('institution_cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['institution_id', 'slug']);
            $table->index(['institution_id', 'status']);
        });

        Schema::create('institution_cohort_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_cohort_id')->constrained('institution_cohorts')->cascadeOnDelete();
            $table->foreignId('institution_user_id')->constrained('institution_users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['institution_cohort_id', 'institution_user_id'], 'cohort_member_unique');
            $table->index('institution_user_id');
        });

        Schema::create('institution_cohort_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_cohort_id')->constrained('institution_cohorts')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->date('due_at')->nullable();
            $table->timestamps();

            $table->unique(['institution_cohort_id', 'course_id'], 'cohort_course_unique');
            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_cohort_courses');
        Schema::dropIfExists('institution_cohort_user');
        Schema::dropIfExists('institution_cohorts');
        Schema::dropIfExists('institution_invitations');
    }
};
