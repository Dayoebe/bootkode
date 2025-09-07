<?php

// Migration 6: add_financial_columns_to_existing_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add financial columns to courses table
        Schema::table('courses', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('courses', 'is_paid')) {
                $table->boolean('is_paid')->default(false)->after('price');
            }
            if (!Schema::hasColumn('courses', 'currency')) {
                $table->string('currency', 3)->default('NGN')->after('is_paid');
            }
        });

        // Add financial columns to users table
        Schema::table('users', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('users', 'bank_code')) {
                $table->string('bank_code')->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('users', 'account_number')) {
                $table->string('account_number')->nullable()->after('bank_code');
            }
            if (!Schema::hasColumn('users', 'account_name')) {
                $table->string('account_name')->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('users', 'account_verified')) {
                $table->boolean('account_verified')->default(false)->after('account_name');
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'currency']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_code', 'account_number', 'account_name', 'account_verified']);
        });
    }
};