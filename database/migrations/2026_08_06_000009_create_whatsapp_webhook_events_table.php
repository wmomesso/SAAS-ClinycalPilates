<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('whatsapp_integration_id')->constrained('whatsapp_integrations')->cascadeOnDelete();
            $table->string('provider_event_id', 191);
            $table->string('event', 80);
            $table->string('message_type', 50)->nullable();
            $table->char('sender_hash', 64)->nullable();
            $table->longText('payload');
            $table->string('status', 30)->default('received');
            $table->string('media_path', 1000)->nullable();
            $table->string('media_mime', 100)->nullable();
            $table->longText('transcription')->nullable();
            $table->longText('error')->nullable();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['whatsapp_integration_id', 'provider_event_id'], 'wa_webhook_event_unique');
            $table->index(['clinic_id', 'received_at']);
            $table->index(['user_id', 'received_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_webhook_events');
    }
};
