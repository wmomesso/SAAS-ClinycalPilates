<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('professional_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->foreignId('service_type_id')->nullable()->constrained('service_types')->onDelete('set null');

            $table->dateTime('start_time');
            $table->dateTime('end_time');

            $table->string('status')->default('scheduled')
                ->comment('Ex: scheduled, confirmed, completed, canceled, no_show');

            $table->text('notes')->nullable();

            // Para recorrências
            $table->string('recurrence_rule')->nullable()->comment('Regra de recorrência (ex: RRULE do iCalendar)');
            $table->unsignedBigInteger('parent_appointment_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
