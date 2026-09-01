<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Discipline
        Schema::create('discipline_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->date('incident_date');
            $table->string('category'); // e.g. Absenteeism, Fighting, Bullying
            $table->text('description');
            $table->text('witnesses')->nullable();
            $table->text('action_taken')->nullable();
            $table->string('sanction')->nullable();
            $table->enum('status', ['open', 'resolved', 'appealed', 'closed'])->default('open');
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('parent_notified')->default(false);
            $table->timestamp('parent_notified_at')->nullable();
            $table->text('confidential_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Health / Medical (restricted access)
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->string('blood_group')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_alerts')->nullable();  // Critical info shown prominently
            $table->text('chronic_conditions')->nullable();
            $table->timestamps();
        });

        Schema::create('clinic_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->date('visit_date');
            $table->text('complaint');
            $table->text('action_taken')->nullable();
            $table->boolean('referred')->default(false);
            $table->text('referral_notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Boarding / Hostel
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('gender', ['male', 'female', 'mixed'])->default('mixed');
            $table->unsignedInteger('capacity')->default(0);
            $table->string('warden_name')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->cascadeOnDelete();
            $table->string('room_no');
            $table->unsignedInteger('capacity')->default(4);
            $table->timestamps();
            $table->unique(['hostel_id', 'room_no']);
        });

        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('bed_no');
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->timestamps();
            $table->unique(['room_id', 'bed_no']);
        });

        Schema::create('bed_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bed_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // Documents
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type'); // Learner, Staff, Guardian, etc.
            $table->unsignedBigInteger('entity_id');
            $table->string('document_type'); // birth_cert, transfer, consent, etc.
            $table->string('file_name');
            $table->string('file_url');
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('bed_allocations');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('hostels');
        Schema::dropIfExists('clinic_visits');
        Schema::dropIfExists('health_records');
        Schema::dropIfExists('discipline_cases');
    }
};
