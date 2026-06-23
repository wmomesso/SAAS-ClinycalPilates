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
        Schema::table('patients', function (Blueprint $table) {
            $table->text('medications')->nullable()->after('medical_history');
            $table->text('allergies')->nullable()->after('medications');
            $table->text('lifestyle_habits')->nullable()->after('allergies');
            $table->string('blood_type')->nullable()->after('lifestyle_habits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['medications', 'allergies', 'lifestyle_habits', 'blood_type']);
        });
    }
};
