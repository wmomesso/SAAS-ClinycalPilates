<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subdomain')->unique()->nullable();
            $table->string('document')->nullable()->comment('CNPJ ou CPF');
            $table->string('logo_path', 2048)->nullable();

            // Chave estrangeira para o dono da clínica
            $table->unsignedBigInteger('owner_id')->nullable();
            // $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
