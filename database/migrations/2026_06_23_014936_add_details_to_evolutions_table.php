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
        Schema::table('evolutions', function (Blueprint $table) {
            $table->string('title')->nullable()->after('professional_id');
            $table->enum('type', ['routine', 'emergency', 'evaluation', 'other'])->default('routine')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evolutions', function (Blueprint $table) {
            $table->dropColumn(['title', 'type']);
        });
    }
};
