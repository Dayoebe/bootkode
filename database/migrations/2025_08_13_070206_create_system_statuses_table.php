<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('system_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Admin who created
            $table->string('service'); // e.g., Website, Database, API
            $table->enum('status', ['operational', 'degraded', 'down', 'maintenance'])->default('operational');
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['low', 'medium', 'high'])->default('low');
            $table->timestamp('started_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_statuses');
    }
}