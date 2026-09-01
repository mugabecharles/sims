<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();   // snapshot in case user deleted
            $table->string('action');                  // create, update, delete, login, etc.
            $table->string('entity')->nullable();      // model name e.g. Learner
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_label')->nullable(); // human-readable e.g. "John Doe (ADM-001)"
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('module')->nullable();
            $table->timestamp('created_at')->useCurrent();
            // audit_logs are immutable: no updated_at, no soft deletes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
