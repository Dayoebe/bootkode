<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
    
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assessments MODIFY COLUMN type ENUM('quiz', 'project', 'assignment', 'qna') DEFAULT 'quiz'");
        }
        
    
    }

    public function down()
    {
        // For MySQL: Revert back to the original ENUM values
        if (DB::getDriverName() === 'mysql') {
            // First update any 'qna' records to 'quiz'
            DB::table('assessments')
                ->where('type', 'qna')
                ->update(['type' => 'quiz']);
                
            // Then modify the column back to original ENUM
            DB::statement("ALTER TABLE assessments MODIFY COLUMN type ENUM('quiz', 'project', 'assignment') DEFAULT 'quiz'");
        }
    }
};