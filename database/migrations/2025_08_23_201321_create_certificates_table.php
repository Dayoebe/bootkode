<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            
            // Certificate Identifiers
            $table->string('certificate_number', 50)->unique();
            $table->string('verification_code', 32)->unique();
            
            // Status and Workflow
            $table->enum('status', ['requested', 'pending', 'approved', 'rejected', 'revoked'])
                  ->default('requested');
            
            // Request Information
            $table->timestamp('requested_at');
            
            // Approval Information
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Rejection Information
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            
            // Revocation Information
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('revocation_reason')->nullable();
            
            // Certificate Content
            $table->timestamp('issued_date')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->string('grade', 10)->nullable();
            $table->integer('credits')->nullable();
            
            // Template and Assets
            $table->string('certificate_template', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->string('verification_url')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('pdf_path')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['user_id', 'course_id']);
            $table->index('status');
            $table->index('verification_code');
            $table->index('certificate_number');
            $table->index('approved_at');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};