<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Wallets
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('wallet_type')->default('user');
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->decimal('pending_balance', 15, 2)->default(0.00);
            $table->string('currency', 3)->default('NGN');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'wallet_type']);
            $table->index(['wallet_type', 'is_active']);
        });

        // Wallet Transactions
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_id')->unique();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('type'); // credit, debit
            $table->string('category'); // funding, course_purchase, instructor_earning, withdrawal, refund
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference')->nullable();
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->string('status')->default('completed');
            
            $table->string('transactionable_type', 50);
            $table->unsignedBigInteger('transactionable_id');
            
            $table->timestamps();

            $table->index(['transactionable_type', 'transactionable_id'], 'wallet_trans_transable_index');
            $table->index(['wallet_id', 'type', 'created_at']);
            $table->index(['reference']);
            $table->index(['category', 'status']);
        });

        // Paystack Transactions
        Schema::create('paystack_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('paystack_reference')->nullable();
            $table->string('access_code')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('status');
            $table->string('gateway_response')->nullable();
            $table->json('paystack_response')->nullable();
            $table->string('customer_email');
            $table->string('customer_name')->nullable();
            $table->string('transaction_type');
            
            $table->string('transactionable_type', 50);
            $table->unsignedBigInteger('transactionable_id');
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['transactionable_type', 'transactionable_id'], 'paystack_trans_transable_index');
            $table->index(['reference', 'status']);
            $table->index(['customer_email', 'status']);
        });

        // Revenue Splits
        Schema::create('revenue_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('instructor_percentage', 5, 2)->default(80.00);
            $table->decimal('platform_percentage', 5, 2)->default(20.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['course_id']);
            $table->index(['instructor_id', 'is_active']);
        });

        // Withdrawals
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->uuid('withdrawal_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('bank_code');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('status')->default('pending');
            $table->string('paystack_transfer_code')->nullable();
            $table->string('paystack_recipient_code')->nullable();
            $table->text('admin_note')->nullable();
            $table->text('failure_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        // Course Enrollments
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrolled_at')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('bulk_batch_id')->nullable();
            $table->index('bulk_batch_id');
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
            $table->index(['user_id', 'is_completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('revenue_splits');
        Schema::dropIfExists('paystack_transactions');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
