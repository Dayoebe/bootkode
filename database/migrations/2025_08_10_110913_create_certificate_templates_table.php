<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('background_image_path');
            $table->json('content_areas')->comment('JSON defining where to place dynamic content');
            $table->string('default_font')->default('Arial');
            $table->integer('default_font_size')->default(14);
            $table->string('default_font_color')->default('#000000');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificate_templates');
    }
};