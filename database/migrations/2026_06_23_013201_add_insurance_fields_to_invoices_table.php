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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('health_insurance_id')->nullable()->after('patient_id')->constrained('health_insurances')->onDelete('set null');
            $table->foreignId('insurance_guide_id')->nullable()->after('health_insurance_id')->constrained('insurance_guides')->onDelete('set null');
            $table->string('responsible_type')->default('patient')->after('insurance_guide_id')->comment('patient, insurance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['health_insurance_id']);
            $table->dropForeign(['insurance_guide_id']);
            $table->dropColumn(['health_insurance_id', 'insurance_guide_id', 'responsible_type']);
        });
    }
};
