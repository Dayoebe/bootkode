<?php
// Migration 4: create_paystack_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paystack_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('paystack_reference')->nullable();
            $table->string('access_code')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('status'); // pending, success, failed, abandoned
            $table->string('gateway_response')->nullable();
            $table->json('paystack_response')->nullable();
            $table->string('customer_email');
            $table->string('customer_name')->nullable();
            $table->string('transaction_type'); // wallet_funding, withdrawal
            
            // Replace morphs with manual columns to avoid long index names
            $table->string('transactionable_type', 50); // Reduced length
            $table->unsignedBigInteger('transactionable_id');
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Create index with custom name to avoid length issues
            $table->index(['transactionable_type', 'transactionable_id'], 'paystack_trans_transable_index');
            
            $table->index(['reference', 'status']);
            $table->index(['customer_email', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('paystack_transactions');
    }
};