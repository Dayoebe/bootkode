<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('commercial_documents')) {
            Schema::create('commercial_documents', function (Blueprint $table) {
                $table->id();
                $table->string('document_number')->unique();
                $table->string('type')->index();
                $table->string('status')->default('issued')->index();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->nullableMorphs('documentable');
                $table->foreignId('paystack_transaction_id')->nullable()->constrained('paystack_transactions')->nullOnDelete();
                $table->foreignId('marketplace_order_id')->nullable()->constrained('marketplace_orders')->nullOnDelete();
                $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->nullOnDelete();
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable()->index();
                $table->string('currency', 3)->default('NGN');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount_total', 15, 2)->default(0);
                $table->decimal('tax_total', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->decimal('amount_paid', 15, 2)->default(0);
                $table->decimal('amount_refunded', 15, 2)->default(0);
                $table->date('issued_on')->nullable();
                $table->date('due_on')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('refunded_at')->nullable();
                $table->json('line_items')->nullable();
                $table->json('metadata')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['type', 'status', 'created_at']);
                $table->index(['user_id', 'type']);
            });
        }

        if (! Schema::hasTable('refund_requests')) {
            Schema::create('refund_requests', function (Blueprint $table) {
                $table->id();
                $table->string('refund_number')->unique();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('paystack_transaction_id')->nullable()->constrained('paystack_transactions')->nullOnDelete();
                $table->foreignId('marketplace_order_id')->nullable()->constrained('marketplace_orders')->nullOnDelete();
                $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->nullOnDelete();
                $table->string('status')->default('requested')->index();
                $table->string('method')->default('manual_ledger');
                $table->decimal('amount', 15, 2);
                $table->string('currency', 3)->default('NGN');
                $table->text('reason');
                $table->text('failure_reason')->nullable();
                $table->string('provider_reference')->nullable()->index();
                $table->json('provider_response')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('requested_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();

                $table->index(['paystack_transaction_id', 'status']);
                $table->index(['user_id', 'status']);
            });
        }

        if (! Schema::hasTable('payout_audits')) {
            Schema::create('payout_audits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('withdrawal_id')->nullable()->constrained('withdrawals')->nullOnDelete();
                $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('event')->index();
                $table->string('status_from')->nullable();
                $table->string('status_to')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('currency', 3)->default('NGN');
                $table->string('provider')->nullable();
                $table->string('provider_reference')->nullable()->index();
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['withdrawal_id', 'created_at']);
                $table->index(['user_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('pricing_packages')) {
            Schema::create('pricing_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('audience')->nullable();
                $table->text('description')->nullable();
                $table->decimal('price', 15, 2)->nullable();
                $table->string('currency', 3)->default('NGN');
                $table->string('interval')->default('one-time');
                $table->json('features')->nullable();
                $table->json('limits')->nullable();
                $table->string('cta_label')->nullable();
                $table->string('cta_route')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_public')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->string('status')->default('active')->index();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['is_public', 'status', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_packages');
        Schema::dropIfExists('payout_audits');
        Schema::dropIfExists('refund_requests');
        Schema::dropIfExists('commercial_documents');
    }
};
