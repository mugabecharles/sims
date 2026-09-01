<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admissions / Applications
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('application_no')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_names')->nullable();
            $table->foreignId('class_applied_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['submitted', 'under_review', 'shortlisted', 'admitted', 'rejected', 'waiting_list', 'withdrawn'])->default('submitted');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Core learner record
        Schema::create('learners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('admission_no')->nullable();
            $table->string('learner_no')->nullable(); // system-generated unique ID
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_names')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->default('Ugandan');
            $table->string('national_id')->nullable();     // NIN if available
            $table->string('birth_cert_no')->nullable();
            $table->string('lin')->nullable();             // EMIS Learner Identification Number if captured
            $table->string('religion')->nullable();
            $table->string('tribe')->nullable();
            $table->text('address')->nullable();
            $table->string('district')->nullable();
            $table->string('subcounty')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();
            $table->text('special_needs')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->text('disability_details')->nullable();
            $table->enum('study_mode', ['day', 'boarding'])->default('day');
            $table->enum('status', ['enrolled', 'suspended', 'withdrawn', 'transferred', 'graduated', 'deceased'])->default('enrolled');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // if learner has portal access
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['school_id', 'admission_no']);
            $table->unique(['school_id', 'learner_no']);
        });

        // Guardians/Parents
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_names')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('phone2', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->string('national_id')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // portal access
            $table->enum('communication_pref', ['sms', 'email', 'both', 'none'])->default('sms');
            $table->boolean('sms_opt_in')->default(true);
            $table->boolean('email_opt_in')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Learner <-> Guardian (many-to-many)
        Schema::create('learner_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->string('relationship'); // Father, Mother, Uncle, etc.
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_fee_payer')->default(false);
            $table->boolean('can_pickup')->default(true);
            $table->timestamps();
            $table->unique(['learner_id', 'guardian_id']);
        });

        // Enrollment: links learner to year/term/class/stream
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('study_mode', ['day', 'boarding'])->default('day');
            $table->enum('status', ['active', 'withdrawn', 'transferred', 'suspended', 'completed'])->default('active');
            $table->date('enrolled_at')->nullable();
            $table->date('left_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['learner_id', 'academic_year_id', 'term_id'], 'unique_enrollment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('learner_guardians');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('learners');
        Schema::dropIfExists('applications');
    }
};
