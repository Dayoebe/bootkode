<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Older MariaDB configurations implicitly made the first TIMESTAMP update
        // on every row change. An explicit default keeps the original start time.
        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->timestamp('started_at')->useCurrent()->change();
        });
    }

    public function down(): void
    {
        // Preserve recorded clocks; never restore an implicit ON UPDATE rule.
    }
};
