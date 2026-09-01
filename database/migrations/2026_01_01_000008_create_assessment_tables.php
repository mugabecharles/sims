<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Assessment periods (Beginning of term, Mid-term, End of term, Mock, etc.)
        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['beginning_of_term', 'mid_term', 'end_of_term', 'mock', 'continuous_assessment', 'project', 'other'])->default('end_of_term');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'published'])->default('draft');
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Assessment definition per period/subject/class
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('assessment_periods')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('weight', 5, 2)->default(100); // percentage weight in final
            $table->foreignId('grading_scheme_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'open', 'submitted', 'approved', 'published'])->default('draft');
            $table->timestamps();
            $table->unique(['period_id', 'subject_id', 'class_id', 'stream_id'], 'unique_assessment');
        });

        // Individual learner scores
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('grade')->nullable();       // A, B, C or D1, D2 etc.
            $table->tinyInteger('points')->nullable();
            $table->text('teacher_comment')->nullable();
            $table->text('initials')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'published'])->default('draft');
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['assessment_id', 'learner_id']);
        });

        // UNEB Examination candidates
        Schema::create('exam_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->enum('exam_type', ['PLE', 'UCE', 'UACE', 'mock', 'internal'])->default('UCE');
            $table->string('exam_year', 4);
            $table->string('centre_no')->nullable();
            $table->string('index_no')->nullable()->unique();
            $table->enum('status', ['registered', 'pending', 'confirmed', 'withdrawn'])->default('pending');
            $table->json('eligibility_checklist')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['learner_id', 'exam_type', 'exam_year']);
        });

        // Subjects per exam candidate
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('exam_candidates')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('subject_code')->nullable();
            $table->enum('ca_status', ['not_required', 'pending', 'submitted', 'approved'])->default('not_required');
            $table->decimal('ca_score', 5, 2)->nullable();
            $table->enum('project_status', ['not_required', 'pending', 'submitted', 'approved'])->default('not_required');
            $table->decimal('project_score', 5, 2)->nullable();
            $table->timestamps();
            $table->unique(['candidate_id', 'subject_id']);
        });

        // Official UNEB results
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('exam_candidates')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('grade')->nullable();
            $table->decimal('mark', 5, 2)->nullable();
            $table->tinyInteger('points')->nullable();
            $table->string('result_year', 4)->nullable();
            $table->enum('source', ['official_import', 'manual_entry', 'api'])->default('manual_entry');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['candidate_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_subjects');
        Schema::dropIfExists('exam_candidates');
        Schema::dropIfExists('assessment_scores');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('assessment_periods');
    }
};
