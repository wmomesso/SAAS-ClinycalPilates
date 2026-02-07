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
        Schema::create('insurance_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('health_insurance_id')->constrained('health_insurances')->onDelete('cascade');

            $table->string('auth_code')->comment('Código de Autorização ou Número da Guia');
            $table->enum('status', ['pending', 'billed', 'paid', 'denied'])
                ->default('pending')
                ->comment('Pendente, Enviado em Lote, Pago pelo Convênio ou Glosado/Negado');

            $table->decimal('total_value', 12, 2)->comment('Valor total esperado do repasse');
            $table->decimal('paid_value', 12, 2)->default(0)->comment('Valor efetivamente pago pelo convênio');

            $table->date('billing_date')->nullable()->comment('Data de envio do faturamento à operadora');
            $table->date('payment_expected_date')->nullable()->comment('Previsão de recebimento');
            $table->date('payment_date')->nullable()->comment('Data real do recebimento do repasse');

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverte a migração.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_guides');
    }
};
