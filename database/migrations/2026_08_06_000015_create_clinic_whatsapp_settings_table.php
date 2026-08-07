<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->unique()->constrained('clinics')->cascadeOnDelete();
            $table->boolean('patient_automation_enabled')->default(false);
            $table->unsignedSmallInteger('reminder_hours_before')->default(24);
            $table->unsignedSmallInteger('reminder_repeat_minutes')->default(180);
            $table->unsignedTinyInteger('reminder_max_attempts')->default(3);
            $table->unsignedSmallInteger('reminder_stop_minutes_before')->default(60);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_whatsapp_settings');
    }
};
