<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->unsignedInteger('missed_sessions')->default(0)->after('used_sessions');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('patient_package_id')
                ->nullable()
                ->after('service_type_id')
                ->constrained('patient_packages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('patient_package_id');
        });

        Schema::table('patient_packages', function (Blueprint $table) {
            $table->dropColumn('missed_sessions');
        });
    }
};
