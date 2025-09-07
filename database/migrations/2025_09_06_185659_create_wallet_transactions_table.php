<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_id')->unique();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('type'); // credit, debit
            $table->string('category'); // funding, course_purchase, instructor_earning, withdrawal, refund
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference')->nullable(); // Paystack reference
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->string('status')->default('completed'); // pending, completed, failed
            
            // Replace morphs with manual columns to avoid long index names
            $table->string('transactionable_type', 50); // Reduced length
            $table->unsignedBigInteger('transactionable_id');
            
            $table->timestamps();

            // Create index with custom name to avoid length issues
            $table->index(['transactionable_type', 'transactionable_id'], 'wallet_trans_transable_index');
            
            $table->index(['wallet_id', 'type', 'created_at']);
            $table->index(['reference']);
            $table->index(['category', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};