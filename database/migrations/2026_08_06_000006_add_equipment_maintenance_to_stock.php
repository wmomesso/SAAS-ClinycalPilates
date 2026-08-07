<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->string('category', 30)->default('consumable')->after('description');
            $table->string('serial_number')->nullable()->after('sku');
            $table->date('acquired_at')->nullable()->after('min_stock_level');
            $table->date('next_maintenance_at')->nullable()->after('acquired_at');
            $table->string('equipment_status', 30)->default('operational')->after('next_maintenance_at');
        });

        Schema::create('equipment_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('performed_at');
            $table->date('next_due_at')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('provider')->nullable();
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance_logs');

        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropColumn(['category', 'serial_number', 'acquired_at', 'next_maintenance_at', 'equipment_status']);
        });
    }
};
