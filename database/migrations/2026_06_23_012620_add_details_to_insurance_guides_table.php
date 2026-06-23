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
        Schema::table('insurance_guides', function (Blueprint $table) {
            $table->string('guide_type')->after('health_insurance_id')->comment('consulta, sp_sadt, etc');
            $table->date('issue_date')->nullable()->after('auth_code');
            $table->date('expiration_date')->nullable()->after('issue_date');
            $table->integer('total_sessions')->default(1)->after('total_value');
            $table->integer('used_sessions')->default(0)->after('total_sessions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurance_guides', function (Blueprint $table) {
            $table->dropColumn(['guide_type', 'issue_date', 'expiration_date', 'total_sessions', 'used_sessions']);
        });
    }
};
