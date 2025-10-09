<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add settings columns if they don't exist
            if (!Schema::hasColumn('users', 'profile_visibility')) {
                $table->enum('profile_visibility', ['public', 'private', 'students'])->default('public')->after('bio');
            }
            
            if (!Schema::hasColumn('users', 'show_email_publicly')) {
                $table->boolean('show_email_publicly')->default(false)->after('profile_visibility');
            }
            
            if (!Schema::hasColumn('users', 'deactivated_at')) {
                $table->timestamp('deactivated_at')->nullable()->after('email_verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_visibility', 'show_email_publicly', 'deactivated_at']);
        });
    }
};