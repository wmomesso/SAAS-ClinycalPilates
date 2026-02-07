<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos relacionados a convênios na tabela de agendamentos.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Indica se o atendimento é via convênio
            $table->boolean('is_insurance')->default(false)->after('service_type_id');

            // Vinculação opcional com uma guia de autorização específica
            $table->foreignId('insurance_guide_id')
                ->nullable()
                ->after('is_insurance')
                ->constrained('insurance_guides')
                ->onDelete('set null');
        });
    }

    /**
     * Reverte as alterações na tabela.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['insurance_guide_id']);
            $table->dropColumn(['is_insurance', 'insurance_guide_id']);
        });
    }
};
