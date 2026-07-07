<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('other');
            $table->boolean('requires_bank_account')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('receivables', function (Blueprint $table) {
            $table->foreignId('payment_method_id')
                ->nullable()
                ->after('payment_method')
                ->constrained('payment_methods')
                ->nullOnDelete();
            $table->nullableMorphs('payment_source');
            $table->date('reconciled_date')->nullable()->after('notes');
            $table->foreignId('reconciled_by')
                ->nullable()
                ->after('reconciled_date')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('payables', function (Blueprint $table) {
            $table->foreignId('payment_method_id')
                ->nullable()
                ->after('amount')
                ->constrained('payment_methods')
                ->nullOnDelete();
            $table->date('reconciled_date')->nullable()->after('notes');
            $table->foreignId('reconciled_by')
                ->nullable()
                ->after('reconciled_date')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropConstrainedForeignId('reconciled_by');
            $table->dropColumn('reconciled_date');
        });

        Schema::table('receivables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropConstrainedForeignId('reconciled_by');
            $table->dropColumn('reconciled_date');
            $table->dropMorphs('payment_source');
        });

        Schema::dropIfExists('payment_methods');
    }
};
