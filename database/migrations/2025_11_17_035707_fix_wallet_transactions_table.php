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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Make transactionable columns nullable if they exist
            if (Schema::hasColumn('wallet_transactions', 'transactionable_type')) {
                $table->string('transactionable_type')->nullable()->change();
            } else {
                $table->string('transactionable_type')->nullable()->after('metadata');
            }

            if (Schema::hasColumn('wallet_transactions', 'transactionable_id')) {
                $table->unsignedBigInteger('transactionable_id')->nullable()->change();
            } else {
                $table->unsignedBigInteger('transactionable_id')->nullable()->after('transactionable_type');
            }

            // Add index for polymorphic relationship if not exists
            if (!Schema::hasIndex('wallet_transactions', 'wallet_transactions_transactionable_type_transactionable_id_index')) {
                $table->index(['transactionable_type', 'transactionable_id'], 'wallet_transactions_transactionable_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Note: We don't drop the columns as they might contain data
            // If you need to rollback completely, uncomment below:
            // $table->dropIndex('wallet_transactions_transactionable_index');
            // $table->dropColumn(['transactionable_type', 'transactionable_id']);
        });
    }
};