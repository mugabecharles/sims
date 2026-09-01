<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('staff_no')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_names')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->default('Ugandan');
            $table->string('national_id')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('photo_url')->nullable();
            $table->enum('staff_type', ['teaching', 'non_teaching', 'support'])->default('teaching');
            $table->string('designation')->nullable();  // Head Teacher, Deputy, Teacher, etc.
            $table->string('department')->nullable();
            $table->date('date_joined')->nullable();
            $table->enum('employment_status', ['active', 'on_leave', 'resigned', 'terminated', 'retired'])->default('active');
            $table->text('qualifications')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['school_id', 'staff_no']);
        });

        // Teacher subject/class assignments per term
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_class_teacher')->default(false);
            $table->timestamps();
            $table->unique(['staff_id', 'class_id', 'subject_id', 'academic_year_id'], 'unique_assignment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('staff');
    }
};
