<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the table exists
        if (!Schema::hasTable('wallet_transactions')) {
            throw new \Exception('wallet_transactions table does not exist!');
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Check if columns exist and add/modify them
            if (Schema::hasColumn('wallet_transactions', 'transactionable_type')) {
                // Column exists, make it nullable
                DB::statement('ALTER TABLE wallet_transactions MODIFY COLUMN transactionable_type VARCHAR(255) NULL');
            } else {
                // Column doesn't exist, add it as nullable
                $table->string('transactionable_type')->nullable()->after('metadata');
            }

            if (Schema::hasColumn('wallet_transactions', 'transactionable_id')) {
                // Column exists, make it nullable
                DB::statement('ALTER TABLE wallet_transactions MODIFY COLUMN transactionable_id BIGINT UNSIGNED NULL');
            } else {
                // Column doesn't exist, add it as nullable
                $table->unsignedBigInteger('transactionable_id')->nullable()->after('transactionable_type');
            }
        });

        // Add index for polymorphic relationship performance
        $indexName = 'wallet_transactions_transactionable_index';
        $indexExists = DB::select(
            "SELECT COUNT(*) as count 
             FROM INFORMATION_SCHEMA.STATISTICS 
             WHERE TABLE_SCHEMA = DATABASE() 
             AND TABLE_NAME = 'wallet_transactions' 
             AND INDEX_NAME = ?",
            [$indexName]
        )[0]->count > 0;

        if (!$indexExists) {
            Schema::table('wallet_transactions', function (Blueprint $table) use ($indexName) {
                $table->index(['transactionable_type', 'transactionable_id'], $indexName);
            });
        }

        // Log the migration
        \Log::info('Wallet transactions table polymorphic columns fixed', [
            'transactionable_type_nullable' => true,
            'transactionable_id_nullable' => true,
            'index_added' => !$indexExists
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop these columns or make them NOT NULL again
        // as it could cause data loss or application errors
        // If you really need to rollback, uncomment the code below:
        
        /*
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('wallet_transactions_transactionable_index');
            // Note: We're NOT dropping the columns as they might contain important data
            // If you need to drop them, uncomment:
            // $table->dropColumn(['transactionable_type', 'transactionable_id']);
        });
        */

        \Log::warning('Wallet transactions polymorphic columns migration rollback attempted - no action taken to preserve data integrity');
    }
};