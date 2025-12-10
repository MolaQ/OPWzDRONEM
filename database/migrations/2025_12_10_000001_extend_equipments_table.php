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
        Schema::table('equipments', function (Blueprint $table) {
            $table->string('serial_number')->nullable()->unique()->after('barcode');
            $table->date('purchase_date')->nullable()->after('description');
            $table->date('warranty_expiry_date')->nullable()->after('purchase_date');
            $table->decimal('cost', 10, 2)->nullable()->after('warranty_expiry_date');
            $table->string('location')->nullable()->after('cost'); // np. "Pracownia 101", "Magazyn"
            $table->enum('condition_status', ['excellent', 'good', 'fair', 'poor'])->default('good')->after('location');
            $table->timestamp('last_maintenance_date')->nullable()->after('condition_status');
            $table->timestamp('next_maintenance_due')->nullable()->after('last_maintenance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropColumn([
                'serial_number',
                'purchase_date',
                'warranty_expiry_date',
                'cost',
                'location',
                'condition_status',
                'last_maintenance_date',
                'next_maintenance_due',
            ]);
        });
    }
};
