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
        // Only add columns if they don't exist
        Schema::table('course_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('course_enrollments', 'enrollment_type')) {
                $table->string('enrollment_type')->default('free')->after('is_completed');
            }
            
            if (!Schema::hasColumn('course_enrollments', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('enrollment_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('course_enrollments', 'enrollment_type')) {
                $table->dropColumn('enrollment_type');
            }
            
            if (Schema::hasColumn('course_enrollments', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }
        });
    }
};