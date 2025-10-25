<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            
            $table->string('certificate_number')->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            
            $table->enum('status', ['pending', 'approved', 'issued', 'revoked'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->enum('certificate_type', ['completion', 'achievement', 'participation'])->default('completion');
            
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_permanent')->default(true);
            
            $table->string('certificate_file')->nullable();
            $table->string('certificate_url')->nullable();
            $table->string('verification_token')->unique()->nullable();
            
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('completion_percentage')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->json('metadata')->nullable();
            $table->json('certificate_data')->nullable();
            
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('revoked_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamp('revoked_at')->nullable();
            $table->text('revocation_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'course_id']);
            $table->index(['status', 'issued_date']);
            $table->index('certificate_number');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};