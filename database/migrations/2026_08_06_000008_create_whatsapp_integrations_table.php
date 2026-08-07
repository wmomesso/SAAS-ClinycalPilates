<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_integrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('provider', 30)->default('uazapi');
            $table->string('base_url', 500);
            $table->string('instance_id', 191)->nullable();
            $table->longText('token');
            $table->char('webhook_secret_hash', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('webhook_registered_at')->nullable();
            $table->timestamps();

            $table->unique('provider');
            $table->unique(['provider', 'instance_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_integrations');
    }
};
