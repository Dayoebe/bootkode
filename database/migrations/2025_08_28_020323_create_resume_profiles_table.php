<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->string('full_name')->nullable();
            $table->string('professional_title')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->text('professional_summary')->nullable();
            $table->string('profile_image')->nullable();
            
            // Experience (JSON array of objects)
            $table->json('work_experience')->nullable();
            
            // Education (JSON array of objects)  
            $table->json('education')->nullable();
            
            // Skills (JSON array with categories and proficiency)
            $table->json('skills')->nullable();
            
            // Projects (JSON array of objects)
            $table->json('projects')->nullable();
            
            // Certifications (JSON array of objects)
            $table->json('certifications')->nullable();
            
            // Languages (JSON array of objects)
            $table->json('languages')->nullable();
            
            // References (JSON array of objects)
            $table->json('references')->nullable();
            
            // Custom sections (JSON for additional sections)
            $table->json('custom_sections')->nullable();
            
            // Template & Styling
            $table->string('selected_template')->default('modern');
            $table->string('color_scheme')->default('professional');
            $table->string('font_family')->default('inter');
            $table->json('section_order')->nullable();
            $table->json('section_visibility')->nullable();
            
            // Settings & Status
            $table->boolean('show_profile_image')->default(true);
            $table->boolean('is_public')->default(false);
            $table->string('public_slug')->nullable()->unique();
            $table->boolean('is_premium')->default(false);
            $table->timestamp('last_edited_at')->nullable();
            
            // Analytics
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_public']);
            $table->index('public_slug');
            $table->index('selected_template');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_profiles');
    }
};