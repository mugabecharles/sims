<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('emis_no')->nullable()->unique();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->enum('level', ['primary', 'secondary', 'combined'])->default('combined');
            $table->enum('ownership', ['government', 'private', 'community', 'religious'])->default('private');
            $table->enum('school_type', ['day', 'boarding', 'mixed'])->default('mixed');
            $table->string('registration_no')->nullable();
            $table->string('licence_no')->nullable();
            $table->string('district')->nullable();
            $table->string('subcounty')->nullable();
            $table->string('village')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('motto')->nullable();
            $table->string('head_teacher_name')->nullable();
            $table->string('head_teacher_signature_url')->nullable();
            $table->string('proprietor_name')->nullable();
            $table->string('stamp_url')->nullable();
            // Numbering formats
            $table->string('admission_no_prefix')->default('ADM');
            $table->integer('admission_no_next')->default(1);
            $table->string('invoice_no_prefix')->default('INV');
            $table->integer('invoice_no_next')->default(1);
            $table->string('receipt_no_prefix')->default('RCP');
            $table->integer('receipt_no_next')->default(1);
            $table->string('learner_id_prefix')->default('STU');
            $table->integer('learner_id_next')->default(1);
            // SMS config
            $table->string('sms_sender_id')->nullable();
            $table->string('sms_provider')->nullable();
            $table->string('sms_api_key')->nullable();
            $table->string('currency', 5)->default('UGX');
            $table->string('timezone')->default('Africa/Kampala');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Link users to school (for multi-school deployments)
        Schema::create('school_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
            $table->unique(['school_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_users');
        Schema::dropIfExists('schools');
    }
};
