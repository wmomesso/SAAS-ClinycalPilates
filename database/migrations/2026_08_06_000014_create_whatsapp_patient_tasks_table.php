<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_patient_tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('webhook_event_id')->nullable()->unique()->constrained('whatsapp_webhook_events')->nullOnDelete();
            $table->string('type', 60);
            $table->string('status', 30)->default('pending');
            $table->unsignedTinyInteger('priority')->default(5);
            $table->char('deduplication_key', 64)->nullable()->unique();
            $table->longText('payload')->nullable();
            $table->longText('result')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->unsignedSmallInteger('max_attempts')->default(5);
            $table->timestamp('available_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->longText('last_error')->nullable();
            $table->timestamps();

            $table->index(['status', 'available_at', 'priority']);
            $table->index(['clinic_id', 'status', 'created_at']);
            $table->index(['patient_id', 'type', 'created_at']);
            $table->index(['appointment_id', 'type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_patient_tasks');
    }
};
