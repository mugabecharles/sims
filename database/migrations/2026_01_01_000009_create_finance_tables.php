<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fee structures (by year/term/class/study_mode)
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('study_mode', ['day', 'boarding', 'all'])->default('all');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Individual fee items within a structure
        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name'); // Tuition, Boarding, Meals, Development, etc.
            $table->enum('category', ['tuition', 'boarding', 'meals', 'development', 'examination', 'transport', 'uniform', 'books', 'activities', 'other'])->default('other');
            $table->bigInteger('amount'); // UGX stored as integer
            $table->boolean('mandatory')->default(true);
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Invoices (debit notes)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_no')->unique();
            $table->bigInteger('total_amount'); // UGX
            $table->bigInteger('total_paid')->default(0);
            $table->bigInteger('balance')->default(0);
            $table->enum('status', ['draft', 'issued', 'partially_paid', 'paid', 'overdue', 'cancelled', 'written_off'])->default('draft');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Invoice line items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->bigInteger('amount');
            $table->timestamps();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_no')->unique();
            $table->bigInteger('amount'); // UGX
            $table->enum('method', ['cash', 'bank', 'mobile_money', 'card', 'gateway', 'other'])->default('cash');
            $table->string('reference')->nullable();   // bank ref, mobile money transaction ID
            $table->string('mobile_number', 20)->nullable();
            $table->string('bank_name')->nullable();
            $table->timestamp('received_at');
            $table->enum('status', ['pending', 'confirmed', 'reversed', 'refunded'])->default('confirmed');
            $table->foreignId('received_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reversed_at')->nullable();
            $table->text('reversal_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            // No soft deletes on payments — use reversal only
        });

        // Payment allocations to invoices
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('amount');
            $table->timestamps();
            $table->unique(['payment_id', 'invoice_id']);
        });

        // Discounts, waivers and scholarships
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['scholarship', 'staff_child', 'sibling', 'bursary', 'waiver', 'other'])->default('waiver');
            $table->bigInteger('amount');
            $table->text('reason');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('fee_items');
        Schema::dropIfExists('fee_structures');
    }
};
