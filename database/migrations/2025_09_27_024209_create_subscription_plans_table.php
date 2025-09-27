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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('stripe_plan_id')->unique()->comment('O ID do plano correspondente no Stripe');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();

            // Limites do plano
            $table->unsignedInteger('limit_professionals')->nullable()->comment('Null para ilimitado');
            $table->unsignedInteger('limit_patients')->nullable()->comment('Null para ilimitado');
            $table->unsignedInteger('limit_rooms')->nullable()->comment('Null para ilimitado');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
