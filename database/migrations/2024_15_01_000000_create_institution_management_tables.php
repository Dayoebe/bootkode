<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Institutions
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->enum('institution_type', ['university', 'college', 'school', 'training_center', 'corporate', 'government', 'non_profit', 'other'])->default('other');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended', 'expired', 'cancelled'])->default('pending');
            $table->enum('license_type', ['basic', 'standard', 'premium', 'enterprise', 'custom'])->default('basic');
            $table->integer('max_users')->default(100);
            $table->integer('current_users')->default(0);
            $table->timestamp('license_start_date')->nullable();
            $table->timestamp('license_end_date')->nullable();
            $table->foreignId('admin_user_id')->nullable()->constrained('users');
            $table->string('billing_email')->nullable();
            $table->text('billing_address')->nullable();
            $table->json('settings')->nullable();
            $table->json('whitelabel_settings')->nullable();
            $table->string('api_key')->unique();
            $table->integer('total_courses_accessed')->default(0);
            $table->integer('total_certificates_issued')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'license_end_date']);
            $table->index('institution_type');
            $table->index('created_by');
            $table->index('slug');
            $table->index('api_key');
        });

        // Institution Users
        Schema::create('institution_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'instructor', 'student', 'observer'])->default('student');
            $table->string('department')->nullable();
            $table->string('employee_id')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['institution_id', 'user_id']);
            $table->index(['institution_id', 'status']);
            $table->index('user_id');
            $table->index('role');
        });

        // Institution License Histories
        Schema::create('institution_license_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['created', 'activated', 'renewed', 'upgraded', 'downgraded', 'suspended', 'cancelled', 'expired']);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('performed_by')->constrained('users');
            $table->timestamp('performed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['institution_id', 'action']);
            $table->index('performed_by');
            $table->index('performed_at');
        });

        // Bulk Enrollment Batches
        Schema::create('bulk_enrollment_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('original_filename');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->integer('total_records')->default(0);
            $table->integer('processed_records')->default(0);
            $table->integer('successful_enrollments')->default(0);
            $table->integer('failed_enrollments')->default(0);
            $table->json('errors')->nullable();
            $table->json('courses')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'status']);
            $table->index('created_by');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_enrollment_batches');
        Schema::dropIfExists('institution_license_histories');
        Schema::dropIfExists('institution_users');
        Schema::dropIfExists('institutions');
    }
};
