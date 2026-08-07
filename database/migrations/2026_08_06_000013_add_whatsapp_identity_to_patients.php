<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->char('whatsapp_phone_hash', 64)->nullable()->after('phone')->index();
        });

        Schema::table('whatsapp_webhook_events', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->after('user_id')->constrained('patients')->nullOnDelete();
            $table->index(['patient_id', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_webhook_events', function (Blueprint $table) {
            $table->dropIndex(['patient_id', 'received_at']);
            $table->dropConstrainedForeignId('patient_id');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('whatsapp_phone_hash');
        });
    }
};
