<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Academic Years
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('year'); // e.g. 2026
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['upcoming', 'active', 'completed', 'archived'])->default('upcoming');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->unique(['school_id', 'year']);
        });

        // Terms
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name');        // Term 1, Term 2, Term 3
            $table->tinyInteger('term_no'); // 1, 2, 3
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->unique(['academic_year_id', 'term_no']);
        });

        // Classes (P1-P7, S1-S6)
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');                // P1, P2, S1, S2, etc.
            $table->string('display_name')->nullable(); // Primary One, Senior One
            $table->enum('level', ['primary', 'secondary'])->default('secondary');
            $table->enum('section', ['o_level', 'a_level', 'primary'])->default('o_level');
            $table->tinyInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['school_id', 'name']);
        });

        // Streams (A, B, C, Arts, Sciences, etc.)
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->string('name');         // A, B, Arts, Sciences, etc.
            $table->string('display_name')->nullable();
            $table->unsignedInteger('capacity')->default(45);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['class_id', 'name']);
        });

        // Subject master catalogue
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->enum('level', ['primary', 'secondary', 'both'])->default('both');
            $table->enum('subject_type', ['compulsory', 'optional', 'elective'])->default('compulsory');
            $table->string('department')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['school_id', 'name']);
        });

        // Subjects offered per class per academic year
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->boolean('compulsory')->default(true);
            $table->timestamps();
            $table->unique(['class_id', 'subject_id', 'academic_year_id']);
        });

        // Grading schemes (configurable per level/year)
        Schema::create('grading_schemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('level', ['primary', 'secondary', 'both'])->default('both');
            $table->json('rules'); // [{"min":80,"max":100,"grade":"D1","points":1,"remark":"Distinction"},...]
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_schemes');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('streams');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('academic_years');
    }
};
