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
        Schema::table('health_insurances', function (Blueprint $table) {
            // Dados da empresa
            $table->string('company_name')->nullable()->after('name')->comment('Razão Social');
            $table->string('cnpj')->nullable()->after('company_name');
            $table->string('email')->nullable()->after('cnpj');
            $table->string('phone')->nullable()->after('email');

            // Endereço
            $table->string('zip_code')->nullable()->after('phone');
            $table->string('address')->nullable()->after('zip_code');
            $table->string('address_number')->nullable()->after('address');
            $table->string('complement')->nullable()->after('address_number');
            $table->string('neighborhood')->nullable()->after('complement');
            $table->string('city')->nullable()->after('neighborhood');
            $table->string('state', 2)->nullable()->after('city');

            // Configurações de Guia e Pagamento
            $table->json('accepted_guide_types')->nullable()->after('is_active')->comment('Tipos de guias aceitas: consulta, sp_sadt, etc');
            $table->string('payment_method')->nullable()->after('accepted_guide_types')->comment('Forma de pagamento: transferencia, cheque, etc');
            $table->integer('payment_terms_days')->default(30)->after('payment_method')->comment('Prazo de pagamento em dias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_insurances', function (Blueprint $table) {
            $table->dropColumn([
                'company_name', 'cnpj', 'email', 'phone',
                'zip_code', 'address', 'address_number', 'complement', 'neighborhood', 'city', 'state',
                'accepted_guide_types', 'payment_method', 'payment_terms_days',
            ]);
        });
    }
};
