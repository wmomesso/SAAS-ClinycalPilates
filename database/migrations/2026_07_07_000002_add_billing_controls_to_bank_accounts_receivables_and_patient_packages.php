<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->boolean('has_pix')->default(false)->after('initial_balance');
            $table->string('pix_key_type')->nullable()->after('has_pix');
            $table->string('pix_key')->nullable()->after('pix_key_type');
            $table->boolean('issues_bank_slips')->default(false)->after('pix_key');
        });

        Schema::table('receivables', function (Blueprint $table) {
            $table->foreignId('patient_package_id')
                ->nullable()
                ->after('invoice_id')
                ->constrained('patient_packages')
                ->nullOnDelete();
            $table->string('payment_method')->nullable()->after('amount');
        });

        Schema::table('patient_packages', function (Blueprint $table) {
            $table->foreignId('bank_account_id')
                ->nullable()
                ->after('invoice_id')
                ->constrained('bank_accounts')
                ->nullOnDelete();
            $table->string('billing_type')->default('single')->after('price_paid');
            $table->string('payment_method')->nullable()->after('billing_type');
            $table->unsignedTinyInteger('billing_day')->nullable()->after('payment_method');
            $table->date('next_billing_date')->nullable()->after('billing_day');
            $table->timestamp('canceled_at')->nullable()->after('next_billing_date');
        });
    }

    public function down(): void
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bank_account_id');
            $table->dropColumn([
                'billing_type',
                'payment_method',
                'billing_day',
                'next_billing_date',
                'canceled_at',
            ]);
        });

        Schema::table('receivables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('patient_package_id');
            $table->dropColumn('payment_method');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'has_pix',
                'pix_key_type',
                'pix_key',
                'issues_bank_slips',
            ]);
        });
    }
};
