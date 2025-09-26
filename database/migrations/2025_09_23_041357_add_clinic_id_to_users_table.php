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
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a chave estrangeira para a clínica.
            // É 'nullable' para permitir usuários 'Super Admin' que não pertencem a nenhuma clínica.
            $table->foreignId('clinic_id')
                ->nullable()
                ->comment('A qual clínica este usuário pertence. NULL para admins do sistema.')
                ->constrained('clinics')
                ->onDelete('cascade');

            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
        });

        // Adiciona a referência de owner_id em clinics após a tabela users ser modificada
        Schema::table('clinics', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn(['clinic_id', 'phone', 'is_active']);
        });
    }
};
