<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('subscription_plans', 'billing_period')) {
                $table->string('billing_period')->default('monthly')->after('price');
            }

            if (! Schema::hasColumn('subscription_plans', 'limit_secretaries')) {
                $table->unsignedInteger('limit_secretaries')->nullable()->after('limit_professionals')->comment('Null para ilimitado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_plans', 'billing_period')) {
                $table->dropColumn('billing_period');
            }

            if (Schema::hasColumn('subscription_plans', 'limit_secretaries')) {
                $table->dropColumn('limit_secretaries');
            }
        });
    }
};
