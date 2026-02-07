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
        Schema::create('health_insurances', function (Blueprint $table) {
            $table->id();
            // Isolamento por Clínica (Tenant)
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');

            $table->string('name')->comment('Nome da Operadora (ex: Unimed, Bradesco)');
            $table->string('registration_number')->nullable()->comment('Número de registro na ANS');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverte a migração.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_insurances');
    }
};
