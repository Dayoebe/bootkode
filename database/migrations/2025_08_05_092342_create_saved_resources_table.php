<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('saved_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('resourceable');
            $table->string('type'); // lesson, note, pdf, video, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'resourceable_type', 'resourceable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saved_resources');
    }
};