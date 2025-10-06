<?php
// Create new migration: php artisan make:migration add_review_fields_to_course_reviews_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('course_reviews', 'helpful_count')) {
                $table->unsignedInteger('helpful_count')->default(0)->after('is_approved');
            }
            if (!Schema::hasColumn('course_reviews', 'instructor_reply')) {
                $table->text('instructor_reply')->nullable()->after('helpful_count');
            }
            if (!Schema::hasColumn('course_reviews', 'replied_at')) {
                $table->timestamp('replied_at')->nullable()->after('instructor_reply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_reviews', function (Blueprint $table) {
            $table->dropColumn(['helpful_count', 'instructor_reply', 'replied_at']);
        });
    }
};