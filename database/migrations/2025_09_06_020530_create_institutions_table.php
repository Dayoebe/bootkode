<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
            $table->enum('institution_type', [
                'university', 'college', 'school', 'training_center',
                'corporate', 'government', 'non_profit', 'other'
            ])->default('other');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended', 'expired', 'cancelled'])
                  ->default('pending');
            $table->enum('license_type', ['basic', 'standard', 'premium', 'enterprise', 'custom'])
                  ->default('basic');
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

            // Add indexes
            $table->index(['status', 'license_end_date']);
            $table->index(['institution_type']);
            $table->index(['created_by']);
            $table->index(['slug']);
            $table->index(['api_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};