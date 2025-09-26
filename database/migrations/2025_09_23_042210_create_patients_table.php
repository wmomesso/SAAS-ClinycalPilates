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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');

            $table->string('full_name');
            $table->date('birth_date');
            $table->string('document_cpf')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            $table->json('address')->nullable()->comment('Ex: { "street": "...", "city": "..." }');
            $table->text('medical_history')->nullable()->comment('Histórico médico, alergias, etc.');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
